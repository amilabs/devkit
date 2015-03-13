<?php if(!defined('AMILABS')) die() ?>
<html>
<head>
  <title>AmiLabs DevKit Demo</title>
  <script src="<?=\AmiLabs\DevKit\Registry::useStorage('ENV')->get('subfolder');?>/js/amilabs.devkit.engine.js"></script>
  <script src="<?=\AmiLabs\DevKit\Registry::useStorage('ENV')->get('subfolder');?>/js/jquery/jquery.min.js"></script>
  <script src="<?=\AmiLabs\DevKit\Registry::useStorage('ENV')->get('subfolder');?>/js/bitcoinjs/bitcoinjs.min.js" ></script>
</head>
<body>
<?=$content?>
</body>
</html>