<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
  <title><?= e($pageTitle ?? 'دليل') ?> — دليل</title>
  <meta name="description" content="<?= e($pageDesc ?? 'منصة دليل التعليمية لكلية الحاسبات والمعلومات — مازن خالد') ?>">
  <link rel="icon" type="image/svg+xml" href="<?= BASE_PATH ?>/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/style.css">
  
  <!-- PWA & Theme -->
  <link rel="manifest" href="<?= BASE_PATH ?>/manifest.json">
  <meta name="theme-color" content="#3b82f6">
  
  <!-- Intro.js for Onboarding Tour -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs-rtl.min.css">
  
  <script>
    (function(){
      var t = localStorage.getItem('dalil-theme') || 'light';
      document.documentElement.setAttribute('data-theme', t);
    })();
  </script>
</head>
<body>
