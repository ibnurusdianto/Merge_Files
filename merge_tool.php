<?php
$defaultSources = ['1.txt', '2.txt'];
$defaultOutput = 'final_result.txt';

$sources = $defaultSources;
$outputFile = $defaultOutput;

function showMenu($sources, $outputFile) {
    echo "\033[1;36m===== File Merge Tool Menu =====\033[0m\n";
    echo "[1] Change source files (Current: " . implode(", ", $sources) . ")\n";
    echo "[2] Change output file (Current: $outputFile)\n";
    echo "[3] Show tool description\n";
    echo "[4] Show author info\n";
    echo "[5] Merge files\n";
    echo "[6] Reset configuration\n";
    echo "[0] Exit\n";
    echo "Select an option: ";
}

function showDescription() {
    echo "\033[1;33mFile Merge Tool\033[0m\n";
    echo "This tool merges multiple text files into a single output file.\n";
    echo "Features: progress bar, backup old output file, multiple output formats (TXT, PDF, DOC, ZIP), reset config.\n\n";
}

function showAuthor() {
    echo "\033[1;32mAuthor:\033[0m buble - Alumni Universitas Pasundan, Poor Code, Junior Pentester\n\n";
}

function readFileLines($filename) {
    if (!file_exists($filename)) {
        echo "\033[1;31mFile not found: $filename\033[0m\n";
        return false;
    }
    if (!is_readable($filename)) {
        echo "\033[1;31mFile not readable: $filename\033[0m\n";
        return false;
    }
    return file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function progressBar($done, $total, $size=30) {
    $perc = (double)($done / $total);
    $bar = floor($perc * $size);
    $status_bar = "\033[1;34m[";
    $status_bar .= str_repeat("=", $bar);
    if ($bar < $size) {
        $status_bar .= ">";
        $status_bar .= str_repeat(" ", $size - $bar - 1);
    }
    else {
        $status_bar .= "=";
    }
    $disp = number_format($perc * 100, 0);
    $status_bar .= "] $disp%  $done/$total\r\033[0m";
    echo $status_bar;
    flush();
}

function backupFile($file) {
    if (file_exists($file)) {
        $backupName = $file . '_' . date('Ymd_His') . '.bak';
        if (!copy($file, $backupName)) {
            echo "\033[1;31mFailed to backup existing output file.\033[0m\n";
            return false;
        }
        echo "\033[1;33mBackup created: $backupName\033[0m\n";
    }
    return true;
}

function saveAsText($outputFile, $contents) {
    return file_put_contents($outputFile, implode(PHP_EOL, $contents) . PHP_EOL);
}

function saveAsPDF($outputFile, $contents) {
    if (!extension_loaded('imagick') && !extension_loaded('pdf')) {
        echo "\033[1;31mPDF output requires additional libraries. Defaulting to TXT.\033[0m\n";
        return saveAsText($outputFile, $contents);
    }
    $contentStr = implode(PHP_EOL, $contents);
    try {
        $pdf = new Imagick();
        $pdf->setResolution(300, 300);
        $draw = new ImagickDraw();
        $draw->setFontSize(12);
        $pdf->newImage(612, 792, new ImagickPixel('white'));
        $pdf->annotateImage($draw, 10, 20, 0, $contentStr);
        $pdf->setImageFormat('pdf');
        $pdf->writeImage($outputFile);
        $pdf->clear();
        $pdf->destroy();
        return true;
    } catch (Exception $e) {
        echo "\033[1;31mError generating PDF: ".$e->getMessage()."\033[0m\n";
        return false;
    }
}

function saveAsDoc($outputFile, $contents) {
    $contentStr = implode("\n", $contents);
    $html = "<html><body><pre>" . htmlspecialchars($contentStr) . "</pre></body></html>";
    return file_put_contents($outputFile, $html);
}

function saveAsZip($outputFile, $contents) {
    $tmpTextFile = 'temp_merge.txt';
    file_put_contents($tmpTextFile, implode(PHP_EOL, $contents) . PHP_EOL);
    $zip = new ZipArchive();
    if ($zip->open($outputFile, ZipArchive::CREATE) !== TRUE) {
        echo "\033[1;31mFailed to create ZIP file.\033[0m\n";
        unlink($tmpTextFile);
        return false;
    }
    $zip->addFile($tmpTextFile, 'merged.txt');
    $zip->close();
    unlink($tmpTextFile);
    return true;
}

function mergeFiles($sources, $outputFile, $outputType) {
    $allLines = [];
    $totalFiles = count($sources);
    $currentFileNum = 0;

    foreach ($sources as $source) {
        $currentFileNum++;
        $lines = readFileLines($source);
        if ($lines === false) {
            return false;
        }
        $totalLines = count($lines);
        echo "\033[1;36mReading file ($currentFileNum/$totalFiles): $source\033[0m\n";
        for ($i=0; $i < $totalLines; $i++) {
            $allLines[] = $lines[$i];
            progressBar($i+1, $totalLines);
            usleep(20000); // Slow progress bar for visibility
        }
        echo "\n";
    }

    if (!backupFile($outputFile)) {
        return false;
    }

    switch ($outputType) {
        case 'txt':
            $result = saveAsText($outputFile, $allLines);
            break;
        case 'pdf':
            $result = saveAsPDF($outputFile, $allLines);
            break;
        case 'doc':
            $result = saveAsDoc($outputFile, $allLines);
            break;
        case 'zip':
            $result = saveAsZip($outputFile, $allLines);
            break;
        default:
            echo "\033[1;31mUnknown output format. Saving as TXT.\033[0m\n";
            $result = saveAsText($outputFile, $allLines);
    }
    if ($result) {
        echo "\033[1;32mFiles merged successfully to $outputFile\033[0m\n";
        return true;
    } else {
        echo "\033[1;31mFailed to save merged file.\033[0m\n";
        return false;
    }
}

function inputSources() {
    echo "Enter source file names separated by commas: ";
    $input = trim(fgets(STDIN));
    $arr = array_map('trim', explode(',', $input));
    if (count($arr) < 1) {
        echo "\033[1;31mNo source files entered.\033[0m\n";
        return false;
    }
    return $arr;
}

function inputOutputFile() {
    echo "Enter output file name (with extension): ";
    $input = trim(fgets(STDIN));
    if ($input == '') {
        echo "\033[1;31mNo output file name entered.\033[0m\n";
        return false;
    }
    return $input;
}

function inputOutputType() {
    echo "Select output format:\n";
    echo "1. TXT (default)\n";
    echo "2. PDF\n";
    echo "3. DOC\n";
    echo "4. ZIP\n";
    echo "Enter choice (1-4): ";
    $choice = trim(fgets(STDIN));
    switch ($choice) {
        case '2':
            return 'pdf';
        case '3':
            return 'doc';
        case '4':
            return 'zip';
        case '1':
        default:
            return 'txt';
    }
}

do {
    showMenu($sources, $outputFile);
    $option = trim(fgets(STDIN));
    switch ($option) {
        case '1':
            $newSources = inputSources();
            if ($newSources !== false) $sources = $newSources;
            break;
        case '2':
            $newOutput = inputOutputFile();
            if ($newOutput !== false) $outputFile = $newOutput;
            break;
        case '3':
            showDescription();
            break;
        case '4':
            showAuthor();
            break;
        case '5':
            $outputType = inputOutputType();
            mergeFiles($sources, $outputFile, $outputType);
            break;
        case '6':
            $sources = $defaultSources;
            $outputFile = $defaultOutput;
            echo "\033[1;33mConfiguration reset to default.\033[0m\n";
            break;
        case '0':
            echo "Exiting...\n";
            exit;
        default:
            echo "\033[1;31mInvalid option. Please choose again.\033[0m\n";
            break;
    }
    echo "\n";
} while (true);
?>
