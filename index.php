<?php
date_default_timezone_set("Asia/Kolkata");

/* ===== SETTINGS ===== */
$ROUND_DURATION = 60;
$RESET_TIME = "05:30:00";
$PREVIEW_AT = 40;
$BASE_SECRET = "k9#A7xP!Q@2026";

/* ===== TIME & RESET ===== */
$now = time();
$todayReset = strtotime(date("Y-m-d $RESET_TIME"));
if ($now < $todayReset) {
    $todayReset = strtotime("-1 day $RESET_TIME");
}
$gameDate = date("Ymd", $todayReset);

/* ===== ROUND ===== */
$elapsed = $now - $todayReset;
$round = floor($elapsed / $ROUND_DURATION) + 1;
$roundPad = str_pad($round, 4, "0", STR_PAD_LEFT);

/* ===== COUNTDOWN ===== */
$sec = $elapsed % 60;
$countdown = 60 - $sec;

/* ===== PREVIEW ===== */
$preview = ($sec >= $PREVIEW_AT);

/* ===== DAILY SECRET ROTATE ===== */
$DAILY_SECRET = hash("sha256", $BASE_SECRET . $gameDate);

/* ===== RNG (0–9 GUARANTEED) ===== */
$seed = $gameDate . $roundPad . $DAILY_SECRET;
$hash = hash("sha256", $seed);

$num = hexdec(substr($hash, -4));
$result = $num % 10;   // 🔒 always 0–9

/* ===== 1 ROUND HISTORY ===== */
$file = __DIR__ . "/_last.json";
$last = file_exists($file) ? json_decode(file_get_contents($file), true) : null;

$period = $gameDate . "10001" . $roundPad;
if (!$last || $last["period"] !== $period) {
    file_put_contents($file, json_encode([
        "period" => $period,
        "result" => $result,
        "time"   => date("H:i")
    ]));
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>VIP ROUND PANEL</title>
<meta http-equiv="refresh" content="1">
<style>
body{font-family:Arial;background:#0d1117;color:#fff;text-align:center}
.box{margin:12px auto;width:320px;padding:14px;border-radius:10px;background:#161b22}
.big{font-size:28px;font-weight:bold}
.preview{color:#00ff99}
</style>
</head>
<body>

<div class="box">
  <div>Server Time</div>
  <div class="big"><?=date("H:i:s")?></div>
</div>

<div class="box">
  <div>Period</div>
  <div class="big"><?=$period?></div>
</div>

<div class="box">
  <div>Round</div>
  <div class="big"><?=$round?></div>
</div>

<div class="box">
  <div>Countdown</div>
  <div class="big"><?=$countdown?></div>
</div>

<!-- NUMBER ALWAYS VISIBLE -->
<div class="box preview">
  <div><?= $preview ? "PREVIEW ACTIVE" : "NEXT NUMBER" ?></div>
  <div class="big" style="opacity:<?= $preview ? "1" : "0.3" ?>">
    <?=$result?>
  </div>
</div>

<?php if($last): ?>
<div class="box">
  <div>Last History</div>
  <div><?=$last["period"]?></div>
  <div><?=$last["result"]?></div>
</div>
<?php endif; ?>

</body>
</html>
