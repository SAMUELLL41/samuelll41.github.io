<?php
$xmlFile = "libconfig_c.dat";
$iconsDir = "images/";

$xml = simplexml_load_file($xmlFile);
$rows = $xml->xpath('//ROW');

$girls = ['cht' => [], 'cbt' => [], 'cpt' => [], 'cha' => []];
$boys = ['cht' => [], 'cbt' => [], 'cpt' => [], 'cha' => []];
$horse = ['haa' => [], 'hab' => [], 'hbd' => [], 'hsh' => []];

function detectType($filename) {
    foreach (['cht', 'cbt', 'cpt', 'cha', 'haa', 'hab', 'hbd', 'hsh'] as $prefix) {
        if (str_contains($filename, $prefix)) return $prefix;
    }
    return null;
}

foreach ($rows as $row) {
    $filename = (string)$row->UIFileName ?: (string)$row->LargeUIFileName;
    $tid = (string)$row->TID;

    if (!$filename || !$tid) continue;

    $imagePath = $iconsDir . $filename . ".png";
    if (!file_exists($imagePath)) continue;

    $type = detectType($filename);
    if (!$type) continue;

    if (str_contains($filename, "r00")) {
        if (isset($girls[$type])) {
            $girls[$type][] = ['img' => $imagePath, 'tid' => $tid];
        }
    } elseif (str_contains($filename, "r02")) {
        if (isset($boys[$type])) {
            $boys[$type][] = ['img' => $imagePath, 'tid' => $tid];
        }
    } elseif (in_array($type, ['haa', 'hab', 'hbd', 'hsh'])) {
        if (isset($horse[$type])) {
            $horse[$type][] = ['img' => $imagePath, 'tid' => $tid];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8" />
    <title>Ikony výber</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4ede4;
            margin: 0;
            padding: 20px;
        }
        h1, h2, h3 {
            color: #4a3f35;
            margin-bottom: 10px;
        }
        .buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        .buttons button {
            padding: 10px 16px;
            font-size: 14px;
            background-color: #e8d5b7;
            border: 1px solid #cbb89f;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: inset 0 -2px 0 #c2a982;
            transition: all 0.2s ease-in-out;
        }
        .buttons button:hover {
            background-color: #d8c4a6;
            box-shadow: inset 0 -2px 0 #b89972;
        }
        .copycommand {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }
        textarea {
            font-size: 14px;
            padding: 10px;
            width: 320px;
            resize: none;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #fffefc;
            margin-bottom: 10px;
        }
        button.copybtn {
            font-size: 14px;
            padding: 10px 20px;
            background-color: #a87e44;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button.copybtn:hover {
            background-color: #916c3a;
        }
        .section {
            display: none;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 24px;
            margin-top: 10px;
            margin-bottom: 40px;
        }
        .item {
            width: 120px;
            background-color: #fef9f1;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            cursor: pointer;
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            border: 2px solid transparent;
        }
        .item:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .item.selected {
            border: 2px solid #28a745;
            background-color: #fedcb4;
        }
        .item.selected::after {
            content: "✔";
            position: absolute;
            top: 6px;
            right: 6px;
            background-color: #28a745;
            color: #b89972;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 14px;
            line-height: 20px;
            text-align: center;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);
        }
        .item img {
            width: 100%;
            border-radius: 8px;
        }
        .item .tid-label {
            margin-top: 6px;
            font-size: 13px;
            color: #6e5b45;
        }
    </style>
</head>
<body>

<h1>Choose a category</h1>

<div class="copycommand">
    <textarea id="commandArea" readonly placeholder="//give item 1 (TID)"></textarea>
    <button class="copybtn" onclick="copyCommand()">Copy command</button>
</div>

<div class="buttons">
    <button onclick="showSection('girlsSection')">Girl items</button>
    <button onclick="showSection('boysSection')">Boy items</button>
    <button onclick="showSection('horseSection')">Horse items</button>
</div>

<div id="girlsSection" class="section">
    <h2>Girl Items</h2>
    <?php
    $girlNames = [
    'cht' => 'Hats',
    'cbt' => 'Dresses',
    'cpt' => 'Legs',
    'cha' => 'Hairs'
    ];
    foreach ($girls as $type => $items): ?>
        <?php if (count($items) > 0): ?>
            <h3><?= $girlNames[$type] ?? strtoupper($type) ?> (<?= $type ?>)</h3>
            <div class="container">
                <?php foreach ($items as $item): ?>
                    <div class="item" onclick="selectTID('<?= $item['tid'] ?>')">
                        <img src="<?= $item['img'] ?>" alt="TID: <?= $item['tid'] ?>" />
                        <div class="tid-label">TID: <?= $item['tid'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<div id="boysSection" class="section">
    <h2>Boy Items</h2>
    <?php
    $boyNames = [
    'cht' => 'Hats',
    'cbt' => 'Dresses',
    'cpt' => 'Legs',
    'cha' => 'Hairs'
    ];
        foreach ($boys as $type => $items): ?>
        <?php if (count($items) > 0): ?>
            <h3><?= $boyNames[$type] ?? strtoupper($type) ?> (<?= $type ?>)</h3>
            <div class="container">
                <?php foreach ($items as $item): ?>
                    <div class="item" onclick="selectTID('<?= $item['tid'] ?>')">
                        <img src="<?= $item['img'] ?>" alt="TID: <?= $item['tid'] ?>" />
                        <div class="tid-label">TID: <?= $item['tid'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<div id="horseSection" class="section">
    <h2>Horse Items</h2>
    <?php
    $horseNames = [
        'haa' => 'Protector',
        'hab' => 'Shield',
        'hbd' => 'Saddle',
        'hsh' => 'Shield'
    ];
    ?>
    <?php foreach ($horse as $type => $items): ?>
        <?php if (count($items) > 0): ?>
            <h3><?= $horseNames[$type] ?? strtoupper($type) ?> (<?= $type ?>)</h3>
            <div class="container">
                <?php foreach ($items as $item): ?>
                    <div class="item" onclick="selectTID('<?= $item['tid'] ?>')">
                        <img src="<?= $item['img'] ?>" alt="TID: <?= $item['tid'] ?>" />
                        <div class="tid-label">TID: <?= $item['tid'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No items for <?= $horseNames[$type] ?? $type ?></p>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<script>
function showSection(id) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById(id).style.display = 'block';
    clearSelection();
}

function selectTID(tid) {
    const textarea = document.getElementById('commandArea');
    textarea.value = `//give item 1 ${tid}`;
    highlightSelected(tid);
}

function highlightSelected(tid) {
    document.querySelectorAll('.item').forEach(item => {
        if (item.querySelector('.tid-label').textContent.includes(tid)) {
            item.classList.add('selected');
        } else {
            item.classList.remove('selected');
        }
    });
}

function clearSelection() {
    document.querySelectorAll('.item').forEach(item => item.classList.remove('selected'));
    document.getElementById('commandArea').value = '';
}

function copyCommand() {
    const textarea = document.getElementById('commandArea');
    textarea.select();
    document.execCommand('copy');
    alert('Command copied to clipboard!');
}

// Show girls by default on page load
showSection('girlsSection');
</script>

</body>
</html>
