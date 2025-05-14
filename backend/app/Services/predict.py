# predict.py
import sys
import json
import pandas as pd
import mysql.connector
from statsmodels.tsa.arima.model import ARIMAResults

# ───── Input: Year and Month ─────
year = int(sys.argv[1])
month = int(sys.argv[2])
target_date = pd.Timestamp(f"{year}-{month:02d}-01")

# ───── MySQL Connection ─────
db = mysql.connector.connect(
    host="https://cloud.projectm.website/phpmyadmin/",          # or your host if remote
    user="adms",
    password="password",
    database="adms"
)

# ───── Fetch Data ─────
query = """
    SELECT b.name AS Barangay, t.created_at AS Date
    FROM transactions t
    INNER JOIN barangays b ON t.barangay_id = b.id
    WHERE t.created_at BETWEEN '2022-01-01' AND '2024-12-31'
"""
df = pd.read_sql(query, db)
db.close()

df["Date"] = pd.to_datetime(df["Date"])
df["Barangay"] = df["Barangay"].astype(str)
df["YearMonth"] = df["Date"].dt.to_period("M")

# ───── Monthly Aggregation ─────
monthly_total = df.groupby("YearMonth").size()
monthly_total.index = monthly_total.index.to_timestamp()

# ───── Load ARIMA model ─────
model = ARIMAResults.load("model_outputs/arima_model.pkl")

last_date = monthly_total.index[-1]
steps_ahead = (target_date.to_period("M") - last_date.to_period("M")).n

if steps_ahead <= 0:
    predicted_total = 0
else:
    forecast = model.forecast(steps=steps_ahead)
    predicted_total = forecast[-1] if steps_ahead <= len(forecast) else 0

# ───── Distribute to Barangays by Past Proportion ─────
barangay_counts = df.groupby("Barangay").size()
total = barangay_counts.sum()

result = []
for barangay, count in barangay_counts.items():
    weight = count / total if total > 0 else 0
    predicted = int(round(predicted_total * weight))

    if predicted <= 5:
        risk = "Low"
    elif predicted <= 15:
        risk = "Moderate"
    elif predicted <= 30:
        risk = "High"
    else:
        risk = "Critical"

    result.append({
        "name": barangay,
        "predictedCase": predicted,
        "risk_level": risk
    })

# Sort by predicted cases
result = sorted(result, key=lambda x: x["predictedCase"], reverse=True)

# Output to PHP
print(json.dumps(result))
