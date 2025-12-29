<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Setting;
use HeadlessChromium\BrowserFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CouponImageController extends Controller
{
    public function generate(Coupon $coupon): Response
    {
        $coupon->load('campaign');

        $currencySymbol = Setting::getValue('currency_symbol', '$');
        $brandName = Setting::getValue('site_name', 'Coupon System');
        $logoPath = Setting::getValue('logo_path');
        $restaurantType = Setting::getValue('restaurant_type', 'Restaurant');
        $openingHours = Setting::getValue('opening_hours', '9:00 AM - 10:00 PM');
        $closeDate = Setting::getValue('close_date', 'Sunday');

        $logo = null;
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $fullPath = Storage::disk('public')->path($logoPath);
            $mimeType = mime_content_type($fullPath);
            $logo = 'data:'.$mimeType.';base64,'.base64_encode(Storage::disk('public')->get($logoPath));
        }

        $html = view('vendor.open-graphy.templates.coupon', [
            'couponCode' => $coupon->code,
            'value' => number_format($coupon->value, 0),
            'campaignName' => $coupon->campaign->title,
            'expiresAt' => $coupon->expires_at->format('M d, Y'),
            'currencySymbol' => $currencySymbol,
            'brandName' => $brandName,
            'logo' => $logo,
            'restaurantType' => $restaurantType,
            'openingHours' => $openingHours,
            'closeDate' => $closeDate,
        ])->render();

        $imageWidth = 1200;
        $imageHeight = 630;

        try {
            $browser = (new BrowserFactory(config('open-graphy.chrome_binary')))->createBrowser([
                'windowSize' => [$imageWidth, $imageHeight],
            ]);

            $page = $browser->createPage();
            $page->setViewport($imageWidth, $imageHeight);
            $page->setHtml($html, config('open-graphy.render_timeout', 10000));

            // Wait for fonts to load
            usleep(1000000);

            $screenshot = base64_decode($page->screenshot(['captureBeyondViewport' => false])->getBase64());

            $browser->close();

            return response($screenshot, 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename="coupon-'.$coupon->code.'.png"',
            ]);
        } catch (\Throwable $e) {
            abort(500, 'Failed to generate image: '.$e->getMessage());
        }
    }

    public function download(Coupon $coupon): Response
    {
        $coupon->load('campaign');

        $currencySymbol = Setting::getValue('currency_symbol', '$');
        $brandName = Setting::getValue('site_name', 'Coupon System');
        $logoPath = Setting::getValue('logo_path');
        $restaurantType = Setting::getValue('restaurant_type', 'Restaurant');
        $openingHours = Setting::getValue('opening_hours', '9:00 AM - 10:00 PM');
        $closeDate = Setting::getValue('close_date', 'Sunday');

        $logo = null;
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $fullPath = Storage::disk('public')->path($logoPath);
            $mimeType = mime_content_type($fullPath);
            $logo = 'data:'.$mimeType.';base64,'.base64_encode(Storage::disk('public')->get($logoPath));
        }

        $html = view('vendor.open-graphy.templates.coupon', [
            'couponCode' => $coupon->code,
            'value' => number_format($coupon->value, 0),
            'campaignName' => $coupon->campaign->title,
            'expiresAt' => $coupon->expires_at->format('M d, Y'),
            'currencySymbol' => $currencySymbol,
            'brandName' => $brandName,
            'logo' => $logo,
            'restaurantType' => $restaurantType,
            'openingHours' => $openingHours,
            'closeDate' => $closeDate,
        ])->render();

        $imageWidth = 1200;
        $imageHeight = 630;

        try {
            $browser = (new BrowserFactory(config('open-graphy.chrome_binary')))->createBrowser([
                'windowSize' => [$imageWidth, $imageHeight],
            ]);

            $page = $browser->createPage();
            $page->setViewport($imageWidth, $imageHeight);
            $page->setHtml($html, config('open-graphy.render_timeout', 10000));

            // Wait for fonts to load
            usleep(1000000);

            $screenshot = base64_decode($page->screenshot(['captureBeyondViewport' => false])->getBase64());

            $browser->close();

            return response($screenshot, 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="coupon-'.$coupon->code.'.png"',
            ]);
        } catch (\Throwable $e) {
            abort(500, 'Failed to generate image: '.$e->getMessage());
        }
    }
}
