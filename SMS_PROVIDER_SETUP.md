# SMS Provider Setup - SMS.NET.BD

## ✅ Free SMS Service (No Credit Card Required)

Your application now uses **SMS.NET.BD** - a Bangladesh SMS provider that offers **free credits** when you sign up.

## 🚀 Setup Instructions

### Step 1: Create Free Account
1. Go to: https://sms.net.bd/signup/
2. Fill in your details (name, email, phone)
3. Verify your email and phone
4. You'll receive **free SMS credits** automatically

### Step 2: Get Your API Key
1. Log in to https://sms.net.bd
2. Navigate to API section or Settings
3. Copy your API key

### Step 3: Update Configuration
1. Open `.env` file in your project root
2. Find this line:
   ```
   SMS_API_KEY=your_api_key_here
   ```
3. Replace `your_api_key_here` with your actual API key:
   ```
   SMS_API_KEY=abc123xyz789yourkey
   ```

### Step 4: Test SMS
1. Go to http://localhost:8000/sms
2. Enter a Bangladesh phone number (e.g., 01819976364)
3. Type a test message
4. Click "Send SMS"

## 📋 What Changed

### Files Updated:
- ✅ `src/Controllers/SmsController.php` - Now uses SMS.NET.BD API
- ✅ `.env` - Updated SMS configuration

### API Details:
- **Endpoint**: `https://api.sms.net.bd/sendsms`
- **Method**: POST
- **Parameters**:
  - `api_key`: Your API key
  - `msg`: Message text
  - `to`: Phone number (880XXXXXXXXXX or 01XXXXXXXXX)

### Phone Number Format:
The system automatically handles these formats:
- `01819976364` → `01819976364` ✅
- `8801819976364` → `8801819976364` ✅
- `1819976364` → `8801819976364` ✅

## 🔄 Alternative Free Providers (if needed)

If SMS.NET.BD doesn't work for you, try these:

### Option 1: BD Bulk SMS
- Website: https://bdbulksms.net/
- Free Credits: 10 SMS on signup
- Same setup process

### Option 2: SMS.to
- Website: https://sms.to
- Free trial available
- 5-minute setup

## 💡 Tips
- Free credits are limited but renew/can be topped up
- Test with your own phone first
- Check spam folder for verification emails
- Keep your API key secret (never commit to Git)

## ❓ Troubleshooting

### "API key invalid" error
- Make sure you copied the full API key
- Check for extra spaces in `.env`
- Verify account is activated

### "Insufficient balance" error
- You've used all free credits
- Top up your account or use another provider

### Still not working?
- Check SMS logs at http://localhost:8000/sms
- Look at `provider_response` column for error details
- Verify your account is verified (email + phone)