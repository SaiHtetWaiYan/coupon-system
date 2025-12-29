<html>
<head>
    <title>Coupon Image</title>
    <meta name="robots" content="noindex,nofollow">

    @include('open-graphy::partials.font')

    <style>
        :root {
            background: #ffffff !important;
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 1200px !important;
            height: 630px !important;
            max-height: 630px !important;
            min-height: 630px !important;
            overflow: hidden !important;
            background: #ffffff !important;
        }

        body {
            font-family: 'Figtree', 'Inter', system-ui, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .coupon-card {
            background: white;
            width: 1020px;
            height: 480px;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            display: flex;
            overflow: hidden;
            position: relative;
        }

        .ribbon {
            position: absolute;
            top: 35px;
            right: -60px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 10px 70px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transform: rotate(45deg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .left-section {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-logo {
            height: 60px;
            width: 60px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 32px;
            font-weight: 700;
            color: #dc2626;
        }

        .restaurant-type {
            font-size: 16px;
            font-weight: 700;
            color: #dc2626;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .brand-info {
            margin-top: 8px;
            font-size: 13px;
            color: #dc2626;
            line-height: 1.6;
        }

        .brand-info span {
            display: block;
        }

        .campaign-name {
            font-size: 24px;
            font-weight: 700;
            color: #dc2626;
            line-height: 1.2;
            margin-top: 20px;
        }

        .value-section {
            margin: 10px 0 30px 0;
            text-align: center;
            background: #fef2f2;
            padding: 15px 25px;
            border-radius: 12px;
            border: 2px dashed #dc2626;
            display: inline-block;
        }

        .value {
            font-size: 72px;
            font-weight: 800;
            color: #dc2626;
            line-height: 1;
        }

        .value-label {
            font-size: 16px;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .expires {
            font-size: 14px;
            color: #9ca3af;
        }

        .right-section {
            width: 350px;
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
        }

        .code-label {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 15px;
            opacity: 0.8;
        }

        .code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 4px;
            background: rgba(255, 255, 255, 0.2);
            padding: 20px 30px;
            border-radius: 12px;
            border: 2px dashed rgba(255, 255, 255, 0.5);
        }

        .scan-text {
            margin-top: 30px;
            font-size: 13px;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="coupon-card">
        <div class="ribbon">Special Offer</div>
        <div class="left-section">
            <div>
                <div class="brand">
                    @if($logo)
                        <img class="brand-logo" src="{{ $logo }}" alt="Logo">
                    @endif
                    <div>
                        <span class="brand-name">{{ $brandName }}</span>
                        <div class="restaurant-type">{{ $restaurantType }}</div>
                    </div>
                </div>
                <h1 class="campaign-name">{{ $campaignName }}</h1>
                <div class="brand-info">
                    <span>Opening Hours <strong>{{ $openingHours }}</strong></span>
                    <span>Closed on <strong>{{ $closeDate }}</strong></span>
                </div>
            </div>

            <div class="value-section">
                <div class="value-label">Save up to</div>
                <div class="value">{{ $currencySymbol }}{{ $value }}</div>
            </div>

            <div class="expires">Valid until {{ $expiresAt }}</div>
        </div>

        <div class="right-section">
            <div class="code-label">Your Code</div>
            <div class="code">{{ $couponCode }}</div>
            <div class="scan-text">Present this code at checkout</div>
        </div>
    </div>
</body>
</html>
