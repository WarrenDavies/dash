<?php 
    include 'keywords.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Keyword Grouper</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <script src='main.js'></script>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
        }
        
        th, td {
            text-align: left;
            padding: 8px;
        }
        
        tr:nth-child(even) {
            background-color: #edfcff;
        }
    </style>
</head>

<body>
    <?php
    echo "OK let's Go! <br/>";
        foreach ($keywords as $keyword) {
            echo ($debug ? "<h3>Testing " . $keyword . "</h3><br/>" : '');
            $words = explode(" ", $keyword);
            foreach ($keywords as $checkAgainst) {
                echo ($debug ? "Testing <b>" . $keyword . "</b> against... <b>" . $checkAgainst . "</b><br/><br/>" : '');
                foreach ($words as $word) {
                    if (strlen($word) == 1 || in_array($word, $exclusions)) {
                        echo ($debug ? $word . " is too small or is excluded. Abort! Abort! <br/><br/>" : '');
                        continue;
                    }
                    echo ($debug ? "Checking " . $word . " against " . $checkAgainst . "<br/>" : '');
                    if( strpos( $checkAgainst, $word ) !== false) {
                        echo ($debug ? "<b>Match!</b> <br/>" : '');
                        $positionToPush = false;
                        if( array_key_exists($word, $groups) ) {
                            $capturedAlready = false;
                            if (in_array($checkAgainst, $groups[$word])) {
                                $capturedAlready = true;
                            }
                            if (!$capturedAlready) {
                                $positionToPush = count($groups[$word]);
                                echo ($debug ? "Array key exists!!<br/>" : '');
                            } else {
                                echo ($debug ? "... but we've already got that one.<br/><br/>" : '');
                            }
                        } else {
                            $groups[$word] = [];
                            $positionToPush = 0;
                        }
                        if ($positionToPush !== false) {
                            echo ($debug ? "adding " . $checkAgainst . " to " . $word . "<br/><br/>" : '');
                            $groups[$word][$positionToPush] = $checkAgainst;
                        }
                    } else {
                        echo ($debug ? "No match. <br/><br/>" : '');
                    }
                }
            }
        }
        foreach ($groups as $key => $value) {
            $totalSearchVolume = 0;
            $totalKeywordDifficulty = 0;
            $totalKeywords = 0;
            $averageKeywordDifficulty = 0;
            foreach ($value as $word => $keyword) {
                $dataKey = trim(str_replace(' ', '_', $keyword));
                if (array_key_exists($dataKey, $data)) {
                        $searchVolume = $data[$dataKey][0];
                    $keywordDifficulty = $data[$dataKey][1];
                    $totalSearchVolume = $totalSearchVolume + $searchVolume;
                    $totalKeywordDifficulty = $totalKeywordDifficulty + $keywordDifficulty;
                    $totalKeywords = $totalKeywords + 1;
                }
            }
            $groups[$key]['totalSearchVolume'] = $totalSearchVolume;
            $groups[$key]['averageKeywordDifficulty'] = $totalKeywordDifficulty / $totalKeywords;
        }

        $tempArr = $sorted = array();
        foreach ($groups as $k => $v) {
            $tempArr[$k] = $groups[$k]['totalSearchVolume'];
        } 
        arsort($tempArr);
        foreach ($tempArr as $k => $v) $sorted[$k] = $groups[$k];

        foreach ($sorted as $key => $value) {
            echo "<h2>" . $key . "</h2>";
            $totalSearchVolume = 0;
            $totalKeywordDifficulty = 0;
            $totalKeywords = 0;
            $averageKeywordDifficulty = 0;
            echo '<table>';
            echo '<tr><th>Keyword</th><th>Search Volume</th><th>Keyword Difficulty</th></tr>';
            foreach ($value as $word => $keyword) {

                if ($keyword == 'totalSearchVolume' || $keyword == 'averageKeywordDifficulty') {
                    continue;
                }
                $dataKey = str_replace(' ', '_', $keyword);
                if (array_key_exists($dataKey, $data)) {
                    $searchVolume = $data[$dataKey][0];
                    $keywordDifficulty = $data[$dataKey][1];
                    $totalSearchVolume = $totalSearchVolume + $searchVolume;
                    $totalKeywordDifficulty = $totalKeywordDifficulty + $keywordDifficulty;
                    $totalKeywords = $totalKeywords + 1;
                    echo '<tr><td>' . $keyword . "</td><td>" . $searchVolume . "</td><td>" . $keywordDifficulty . "</td></tr>";
                }
                
            }
            $averageKeywordDifficulty = $totalKeywordDifficulty / $totalKeywords;
            echo "<tr><td><h4>Total / Average: </h4> " . "</td><td><h4>" . number_format($totalSearchVolume) . "</h4></td><td><h4>" . number_format($averageKeywordDifficulty) . "</h4>";
            echo '</table>';
        } 
    ?>
    
</body>
</html>