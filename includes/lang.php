<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Switch language if requested
if (isset($_GET['lang'])) {
    $requested_lang = $_GET['lang'];
    if (in_array($requested_lang, ['en', 'am'], true)) {
        $_SESSION['lang'] = $requested_lang;
    }
}

$current_lang = $_SESSION['lang'] ?? 'en';

// Translation Dictionary
$translations = [
    'en' => [
        'home' => 'Home',
        'about' => 'About',
        'services' => 'Services',
        'contact' => 'Contact',
        'dashboard' => 'Dashboard',
        'login' => 'Login',
        'register' => 'Register',
        'logout' => 'Logout',
        'welcome' => 'Welcome to Besufkad Gym',
        'subtitle' => 'Transform your body and mind with state-of-the-art equipment, professional trainers, and engaging group classes.',
        'why_choose' => 'Why Choose Us?',
        'why_desc' => 'We provide everything you need to reach your peak potential.',
        'equip_title' => '💪 Modern Equipment',
        'equip_desc' => 'Top-tier cardio, strength training, and functional fitness gear available 24/7.',
        'trainers_title' => '🏆 Expert Trainers',
        'trainers_desc' => 'Get personalized guidance, diet plans, and motivation.',
        'classes_title' => '🔥 Dynamic Classes',
        'classes_desc' => 'Join Yoga, HIIT, Cardio, and Bodybuilding classes.',
        'switch_lang' => 'አማርኛ',
        'switch_lang_code' => 'am',
    ],
    'am' => [
        'home' => 'መነሻ',
        'about' => 'ስለ እኛ',
        'services' => 'አገልግሎቶች',
        'contact' => 'አድራሻ',
        'dashboard' => 'ዳሽቦርድ',
        'login' => 'ግባ',
        'register' => 'ይመዝገቡ',
        'logout' => 'ውጣ',
        'welcome' => 'እንኳን ወደ በሱፍቃድ ጂም በደህና መጡ',
        'subtitle' => 'በዘመናዊ መሣሪያዎች፣ በባለሙያ አሰልጣኞች እና አሳታፊ የቡድን ትምህርቶች አካልዎን እና አእምሮዎን ያሳድጉ።',
        'why_choose' => 'ለምን እኛን ይመርጣሉ?',
        'why_desc' => 'ከፍተኛ ደረጃ ላይ ለመድረስ የሚፈልጉትን ሁሉ እናቀርባለን።',
        'equip_title' => '💪 ዘመናዊ መሣሪያዎች',
        'equip_desc' => 'ከፍተኛ ደረጃ ያላቸው የካርዲዮ እና የጥንካሬ ማሰልጠኛ መሣሪያዎች 24/7 ይገኛሉ።',
        'trainers_title' => '🏆 ባለሙያ አሰልጣኞች',
        'trainers_desc' => 'ግላዊ መመሪያ፣ የአመጋገብ እቅዶች እና ማበረታቻ ያግኙ።',
        'classes_title' => '🔥 ተለዋዋጭ ክፍሎች',
        'classes_desc' => 'የዮጋ፣ የካርዲዮ እና የሰውነት ግንባታ ክፍሎችን ይቀላቀሉ።',
        'switch_lang' => 'English',
        'switch_lang_code' => 'en',
    ]
];

$t = $translations[$current_lang];
?>