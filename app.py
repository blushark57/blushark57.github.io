from flask import Flask, render_template,request, jsonify
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Enable Cross-Origin Resource Sharing (if needed)

# Email Configuration
SMTP_SERVER = "smtp.gmail.com"  # Replace with your SMTP server
SMTP_PORT = 587  # Typically 587 for TLS
EMAIL_ADDRESS = "arbaz57@gmail.com"  # Replace with your email
EMAIL_PASSWORD = "xhca kybk qvsl sgrl"  # Replace with your password

@app.route("/")
def index():
    # Serve the index.html file
    return render_template("index.html")

@app.route("/send-email", methods=["POST"])
def send_email():
    try:
        # Get form data
        data = request.json
        name = data.get("name")
        email = data.get("email")
        subject = data.get("subject")
        message = data.get("message")

        # Create the email
        msg = MIMEMultipart()
        msg["From"] = EMAIL_ADDRESS
        msg["To"] = EMAIL_ADDRESS  # Send to yourself or a recipient
        msg["Subject"] = subject

        # Email Body
        body = f"From: {name}\nEmail: {email}\n\nMessage:\n{message}"
        msg.attach(MIMEText(body, "plain"))

        # Connect to SMTP Server
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            server.starttls()  # Secure the connection
            server.login(EMAIL_ADDRESS, EMAIL_PASSWORD)
            server.sendmail(EMAIL_ADDRESS, EMAIL_ADDRESS, msg.as_string())

        return jsonify({"success": True, "message": "Email sent successfully!"})

    except Exception as e:
        return jsonify({"success": False, "message": str(e)}), 500

if __name__ == "__main__":
    app.run(debug=True)
