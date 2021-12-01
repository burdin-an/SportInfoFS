<?php
/**
 * Проект "Информатор спортивных соревнований: фигурное катание на коньках"
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    Бурдин А.Н. <support@it-sakh.net>
 * @copyright Бурдин А.Н. <support@it-sakh.net>
 * @link      http://www.it-sakh.info/SportInfo/
 * @link      https://github.com/burdin-an/SportInfoFS
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   1.0.4
 */

setlocale(LC_CTYPE, 'ru_RU.UTF-8');
error_reporting(E_ALL ^ E_WARNING);

// Обрабатываем конфигурационный файл по-умолчанию: config-default.ini
$configDefault = parse_ini_file(__DIR__ . "/config-default.ini");
// Обрабатываем локальный конфигурационный файл: config-local.ini
if (file_exists(__DIR__ . "/config-local.ini")) {
    $configLocal = parse_ini_file(__DIR__ . "/config-local.ini");
    $ini = array_merge($configDefault, $configLocal);
    unset($configLocal);
}
else {
    $ini = $configDefault;
}

unset($configDefault);

if (!is_array($ini)) {
    print_r($ini);
    echo "Не удалось прочитать конфигурационный файл.\n";
    exit;
}

$EventDB = [];
$timeOldCheckAction = -1;
//Start List (STL) Стартовый лист
//Warm Group (WUP) Список группы разминки
//3nd Score (3SC) Список промежуточных результатов соревнования
function ActionGroup($CommandAction,$ParticipantID) {
    global $EventDB;
    $ReturnJsonToWeb = [
        "timestamp"    => time(),
        "EventName"  => (string)$EventDB["Name"],
        "pCategory"  => (string)$EventDB["Category"]["Name"],
        "pSegment"   => (string)$EventDB["Segment"]["Name"],
        "pParticipant" => [],
    ];

    echo "---------------------------------------------------------------------\n";
    if ($CommandAction == 'STL') {
        $ReturnJsonToWeb["dAction"] = 'STL';
        echo "Action: STL;\n";
    }
    elseif ($CommandAction == 'WUP') {
        $ReturnJsonToWeb["dAction"] = 'WUP';
        $ReturnJsonToWeb["pCurrentGroup"] = (int)$EventDB['Participants']["p-" . $ParticipantID]['GroupNumber'];
        echo "Action: WUP;\n";
        echo "CurrentGroupNumber: "  . $ReturnJsonToWeb["pGroup"] . ";\n";
    }
    elseif ($CommandAction == '3SC') {
        $ReturnJsonToWeb["dAction"] = '3SC';
        echo "Action: 3SC;\n";
    }
    elseif ($CommandAction == 'IRS') {
        $ReturnJsonToWeb["dAction"] = 'IRS';
        echo "Action: IRS;\n";
    }
    elseif ($CommandAction == 'RES') {
        $ReturnJsonToWeb["dAction"] = 'RES';
        echo "Action: RES;\n";
    }

    echo "EventName: " . $ReturnJsonToWeb['EventName'] . ";\n";
    echo "CategoryName: " . $ReturnJsonToWeb['pCategory'] . ";\n";
    echo "SegmentName: " . $ReturnJsonToWeb['pSegment'] . ";\n";

    foreach ($EventDB['Participants'] as $ParticipantStr) {
        if ($CommandAction == 'STL' || $CommandAction == 'WUP') {
            $idLine = (int)$ParticipantStr['StartNumber'];
        }
        elseif ($CommandAction == '3SC' || $CommandAction == 'IRS' || $CommandAction == 'RES') {
            $idLine = (int)$ParticipantStr['TSort'];
        }
        //Для WUP (Группа разминки)
        //Пропускаем участника не из своей группы разминки
        if ($CommandAction == 'WUP' && $ReturnJsonToWeb["pCurrentGroup"] != $ParticipantStr['GroupNumber']) {
            //echo "StartNumber: "  . $ParticipantStr['StartNumber'] . ";\n";
            //echo "GroupNumber: "  . $ParticipantStr['GroupNumber'] . ";\n";
            continue;
        }

        $ReturnJsonToWeb["pParticipant"][$idLine] = [
            "ID"           => $ParticipantStr["ID"],
            "pStartNumber" => (int)$ParticipantStr["StartNumber"],
            "pGroupNumber" => (int)$ParticipantStr["GroupNumber"],
            "pFullName"    => (string)$ParticipantStr["FullName"],
            "pNation"      => (string)$ParticipantStr["Nation"],
            "pClub"        => (string)$ParticipantStr["Club"],
            "pCity"        => (string)$ParticipantStr["City"],
            "pTRank"       => (int)$ParticipantStr["TRank"],
            "pTPoint"      => (string)$ParticipantStr["TPoint"],
            "pTSort"       => (int)$ParticipantStr["TSort"],
            "pStatus"      => (string)$ParticipantStr["Status"],
            "pCurrent"     => 2
        ];
        if ($ParticipantStr['ID'] === (int)$ParticipantID) {
            $ReturnJsonToWeb["pParticipant"][$idLine]["pCurrent"]  = 1;
        }

        echo "-----------------\n";
        echo "StartLine: "    . $idLine . ";\n";
        echo "ID: "           . $ReturnJsonToWeb["pParticipant"][$idLine]['ID'] . ";\n";
        echo "StartNumber: "  . $ReturnJsonToWeb["pParticipant"][$idLine]['pStartNumber'] . ";\n";
        echo "GroupNumber: "  . $ReturnJsonToWeb["pParticipant"][$idLine]['pGroupNumber'] . ";\n";
        echo "FullName: "     . $ReturnJsonToWeb["pParticipant"][$idLine]['pFullName'] . ";\n";
        echo "Nation: "       . $ReturnJsonToWeb["pParticipant"][$idLine]['pNation'] . ";\n";
        echo "Club: "         . $ReturnJsonToWeb["pParticipant"][$idLine]['pClub'] . ";\n";
        echo "City: "         . $ReturnJsonToWeb["pParticipant"][$idLine]['pCity'] . ";\n";
        echo "TRank: "        . $ReturnJsonToWeb["pParticipant"][$idLine]['pTRank'] . ";\n";
        echo "TPoint: "       . $ReturnJsonToWeb["pParticipant"][$idLine]['pTPoint'] . ";\n";
        echo "TSort: "        . $ReturnJsonToWeb["pParticipant"][$idLine]['pTSort'] . ";\n";
        echo "Status: "       . $ReturnJsonToWeb["pParticipant"][$idLine]['pStatus'] . ";\n";
        if ($CommandAction == '3SC' || $CommandAction == 'IRS' || $CommandAction == 'RES') {
            echo "Current: "  . $ReturnJsonToWeb["pParticipant"][$idLine]["pCurrent"] . ";\n";
        }
    }
    ksort($ReturnJsonToWeb["pParticipant"],0);
    return $ReturnJsonToWeb;
}
//Показать индивидуальные результаты проката 2SC и 1SC
function ActionPersonalResult($dAction,$ParticipantID) {
    global $EventDB;
    $ReturnJsonToWeb = [
        "timestamp"  => time(),
        "dAction"    => (string)$dAction,
        "EventName"  => (string)$EventDB["Name"],
        "pCategory"  => (string)$EventDB["Category"]["Name"],
        "pSegment"   => (string)$EventDB["Segment"]["Name"],
        "pName"      => (string)$EventDB['Participants'][$ParticipantID]['FullName'],
        "pClub"      => (string)$EventDB['Participants'][$ParticipantID]['Club'],
        "pCity"      => (string)$EventDB['Participants'][$ParticipantID]['City'],
        "pNation"    => (string)$EventDB['Participants'][$ParticipantID]['Nation'],
        "pMusic"     => (string)$EventDB['Participants'][$ParticipantID]['Music'],
        "pCoach"     => (string)$EventDB['Participants'][$ParticipantID]['Coach'],
    ];
    echo "---------------------------------------------------------------------\n";
    echo "Action: "   .  $dAction . ";\n";
    echo "Участник: " .  $ReturnJsonToWeb['pName'] . ";\n";
    echo "СШ: " .        $ReturnJsonToWeb['pClub'] . ";\n";
    echo "Город: " .     $ReturnJsonToWeb['pCity'] . ";\n";
    echo "Страна: " .    $ReturnJsonToWeb['pNation'] . ";\n";
    echo "Music: " .     $ReturnJsonToWeb['pMusic'] . ";\n";
    echo "Coach: " .     $ReturnJsonToWeb['pCoach'] . ";\n";
    if ($dAction == "1SC") {
        $ReturnJsonToWeb["pTES"]       = $EventDB['Participants'][$ParticipantID]['TES'];
        $ReturnJsonToWeb["pTCS"]       = $EventDB['Participants'][$ParticipantID]['TCS'];
        $ReturnJsonToWeb["pBonus"]     = $EventDB['Participants'][$ParticipantID]['Bonus'];
        $ReturnJsonToWeb["pDedSum"]    = $EventDB['Participants'][$ParticipantID]['DedSum'];
        $ReturnJsonToWeb["pSeqPoints"] = $EventDB['Participants'][$ParticipantID]['SeqPoints'];
        $ReturnJsonToWeb["pTPoint"]    = $EventDB['Participants'][$ParticipantID]['TPoint'];
        $ReturnJsonToWeb["pTRank"]     = $EventDB['Participants'][$ParticipantID]['TRank'];
        $ReturnJsonToWeb["Element"]    = $EventDB['Participants'][$ParticipantID]['Element'];
        $ReturnJsonToWeb["Deduction"]  = $EventDB['Participants'][$ParticipantID]['Deduction'];
        
        foreach ($EventDB['Participants'] as $ParticipantStr) {
            $idLine = (int)$ParticipantStr['TSort'];
            $ReturnJsonToWeb["Participant"][$idLine] = [
                "ID"       => $ParticipantStr["ID"],
                "FullName" => $ParticipantStr["FullName"],
                "Nation"   => $ParticipantStr["Nation"],
                "Club"     => $ParticipantStr["Club"],
                "City"     => $ParticipantStr["City"],
                "TPoint"   => $ParticipantStr["TPoint"],
                "TSort"    => $ParticipantStr["TSort"],
                "Current"  => 2
            ];
            if ($ParticipantStr['ID'] === (int)$ParticipantID) {
                $ReturnJsonToWeb["Participant"][$idLine]["Current"]  = 1;
            }
        }
        ksort($ReturnJsonToWeb["Participant"],0);
        echo "TES: " .         $ReturnJsonToWeb['pTES'] . ";\n";
        echo "TCS: " .         $ReturnJsonToWeb['pTCS'] . ";\n";
        echo "Bonus: " .       $ReturnJsonToWeb['pBonus'] . ";\n";
        echo "Deduction: " .   $ReturnJsonToWeb['pDedSum'] . ";\n";
        echo "SeqPoints: " .   $ReturnJsonToWeb['pSeqPoints'] . ";\n";
        echo "Total Point: " . $ReturnJsonToWeb['pTPoint'] . ";\n";
        echo "Total Rank: "  . $ReturnJsonToWeb['pTRank'] . ";\n";
    }
    if ($dAction == "2SC") {
        $ReturnJsonToWeb["pTES"]       = (string)$EventDB['Participants'][$ParticipantID]['TES'];
        $ReturnJsonToWeb["pTCS"]       = (string)$EventDB['Participants'][$ParticipantID]['TCS'];
        $ReturnJsonToWeb["pBonus"]     = (string)$EventDB['Participants'][$ParticipantID]['Bonus'];
        $ReturnJsonToWeb["pDedSum"]    = (string)$EventDB['Participants'][$ParticipantID]['DedSum'];
        $ReturnJsonToWeb["pSeqPoints"] = (string)$EventDB['Participants'][$ParticipantID]['SeqPoints'];
        $ReturnJsonToWeb["pRank"]      = (string)$EventDB['Participants'][$ParticipantID]['Rank'];
        echo "TES: " .       $ReturnJsonToWeb['pTES'] . ";\n";
        echo "TCS: " .       $ReturnJsonToWeb['pTCS'] . ";\n";
        echo "Bonus: " .     $ReturnJsonToWeb['pBonus'] . ";\n";
        echo "DedSum: " .    $ReturnJsonToWeb['pDedSum'] . ";\n";
        echo "SeqPoints: " . $ReturnJsonToWeb['pSeqPoints'] . ";\n";
        echo "Rank: " .      $ReturnJsonToWeb['pRank'] . ";\n";
    }
    unset($ParticipantID);    
    return $ReturnJsonToWeb;
}
//
function ActionJudge($JudgeID) {
    global $EventDB;
    if ($JudgeID == -1) {
        $ReturnJsonToWeb = [
            "timestamp"    => time(),
            "dAction"      => 'JudgeAll',
            "EventName"    => (string)$EventDB["Name"],
            "pCategory"    => (string)$EventDB["Category"]["Name"],
            "pSegment"     => (string)$EventDB["Segment"]["Name"],
            "pParticipant" => [],
        ];

        echo "---------------------------------------------------------------------\n";
        echo "Action: JudgeALL;\n";
        echo "EventName: " . $ReturnJsonToWeb['EventName'] . ";\n";
        echo "CategoryName: " . $ReturnJsonToWeb['pCategory'] . ";\n";
        echo "SegmentName: " . $ReturnJsonToWeb['pSegment'] . ";\n";
        $ReturnJsonToWeb["pParticipant"] = $EventDB['Judge'];
    }
    elseif ($JudgeID > 0) {
        $ReturnJsonToWeb = [
            "timestamp"    => time(),
            "dAction"      => 'JudgeOne',
            "EventName"    => (string)$EventDB["Name"],
            "pCategory"    => (string)$EventDB["Category"]["Name"],
            "pSegment"     => (string)$EventDB["Segment"]["Name"],
        ];
        echo "---------------------------------------------------------------------\n";
        echo "Action: JudgeOne;\n";
        echo "EventName: " . $ReturnJsonToWeb['EventName'] . ";\n";
        echo "CategoryName: " . $ReturnJsonToWeb['pCategory'] . ";\n";
        echo "SegmentName: " . $ReturnJsonToWeb['pSegment'] . ";\n";
        foreach ($EventDB['Judge'] as $JudgeStr) {
            if ($JudgeStr["pID"] == $JudgeID) {
                $ReturnJsonToWeb["pIndex"][(int)$JudgeStr["pIndex"]] = (string)$JudgeStr["dFunction"];
                $ReturnJsonToWeb["pName"]   = (string)$JudgeStr["pFullName"];
                $ReturnJsonToWeb["pNation"] = (string)$JudgeStr["pNation"];
                $ReturnJsonToWeb["pClub"]   = (string)$JudgeStr["pClub"];
                echo "-----------------\n";
                echo "Index: "    . $JudgeStr['pIndex'] . ";\n";
                echo "FullName: " . $JudgeStr['pFullName'] . ";\n";
                echo "Nation: "   . $JudgeStr['pNation'] . ";\n";
                echo "Club: "     . $JudgeStr['pClub'] . ";\n";
                echo "Function: " . $JudgeStr['dFunction'] . ";\n";
            }
        }
    }
    else {
        $ReturnJsonToWeb = [
            "timestamp"    => time(),
            "dAction"      => 'JudgeEmpty',
            "EventName"    => (string)$EventDB["Name"],
            "pCategory"    => (string)$EventDB["Category"]["Name"],
            "pSegment"     => (string)$EventDB["Segment"]["Name"],
        ];
        echo "---------------------------------------------------------------------\n";
        echo "Action: JudgeEmpty;\n";
    }
    $JudgeID = 0;
    return $ReturnJsonToWeb;
}
//
function ActionVictory($SubCommandAction) {
    global $EventDB;
    //Приглашение на награждение участников
    if ($SubCommandAction == 5) {
        $ReturnJsonToWeb = [
            "timestamp"    => time(),
            "dAction"      => "VictoryStart",
            "EventName"    => (string)$EventDB["Name"],
            "pCategory"    => (string)$EventDB["Category"]["Name"],
        ];
        echo "---------------------------------------------------------------------\n";
        echo "Action: VictoryStart;\n";
    }
    // Первое место
    elseif ($SubCommandAction == 1 || $SubCommandAction == 2 || $SubCommandAction == 3) {
        $ReturnJsonToWeb = [
            "timestamp" => time(),
            "dAction"   => "VictoryPlace",
            "EventName" => $EventDB["Name"],
            "sAction"   => "",
            "pFullName" => "",
            "pNation"   => "",
            "pClub"     => "",
            "pCity"     => "",
        ];
        if ($SubCommandAction == 1) {
            $ReturnJsonToWeb["sAction"] = "First";
        }
        elseif ($SubCommandAction == 2) {
            $ReturnJsonToWeb["sAction"] = "Second";
        }
        elseif ($SubCommandAction == 3) {
            $ReturnJsonToWeb["sAction"] = "Third";
        }
        foreach ($EventDB['Participants'] as $ParticipantStr) {
            if ($ParticipantStr["TRank"] == (int)$SubCommandAction) {
                print_r($ParticipantStr["TRank"]);
                print_r((int)$SubCommandAction);
                $ReturnJsonToWeb["pFullName"] = $ParticipantStr["FullName"];
                $ReturnJsonToWeb["pNation"]   = $ParticipantStr["Nation"];
                $ReturnJsonToWeb["pClub"]     = $ParticipantStr["Club"];
                $ReturnJsonToWeb["pCity"]     = $ParticipantStr["City"];
            }
        }
        echo "---------------------------------------------------------------------\n";
        echo "Action: Victory" . $ReturnJsonToWeb["sAction"] . ";\n";
        echo "FullName: " . $ReturnJsonToWeb["pFullName"] . ";\n";
        echo "Nation: "   . $ReturnJsonToWeb["pNation"] . ";\n";
        echo "Club: "     . $ReturnJsonToWeb["pClub"] . ";\n";
        echo "City: "     . $ReturnJsonToWeb["pCity"] . ";\n";
    }
    //Подиум (Все места)
    elseif ($SubCommandAction == 0) {
        $ReturnJsonToWeb = [
            "timestamp"    => time(),
            "dAction"      => "VictoryAll",
            "EventName"    => $EventDB["Name"],
            //"pParticipant" => [],
        ];
        echo "---------------------------------------------------------------------\n";
        echo "Action: VictoryAll;\n";
        foreach ($EventDB['Participants'] as $ParticipantStr) {
            foreach (range(1, 3) as $line) {
                if ($ParticipantStr["TRank"] == $line) {
                    $ReturnJsonToWeb["pParticipant"][$ParticipantStr["TRank"]] = [
                        "pFullName" => $ParticipantStr["FullName"],
                        "pNation"   => $ParticipantStr["Nation"],
                        "pClub"     => $ParticipantStr["Club"],
                        "pCity"     => $ParticipantStr["City"],
                        "pTRank"    => $ParticipantStr["TRank"],
                    ];
                    echo "FullName: " . $ParticipantStr["FullName"] . ";\n";
                    echo "Nation: "   . $ParticipantStr["Nation"] . ";\n";
                    echo "Club: "     . $ParticipantStr["Club"] . ";\n";
                    echo "City: "     . $ParticipantStr["City"] . ";\n";
                    echo "Place: "    . $ParticipantStr["TRank"] . ";\n";
                }
            }
        }
        ksort($ReturnJsonToWeb["pParticipant"]);
    }
    return $ReturnJsonToWeb;
}

function ActionSegment() {
    global $EventDB;
    $ReturnJsonToWeb = [
        "timestamp"  => time(),
        "dAction"    => "SEG",
        "EventName"  => (string)$EventDB["Name"],
        "pCategory"  => (string)$EventDB["Category"]["Name"],
        "pSegment"   => (string)$EventDB["Segment"]["Name"],
    ];

    echo "---------------------------------------------------------------------\n";
    echo "Action: Segment;\n";
    echo "EventName: " . $ReturnJsonToWeb['EventName'] . ";\n";
    echo "CategoryName: " . $ReturnJsonToWeb['pCategory'] . ";\n";
    echo "SegmentName: " . $ReturnJsonToWeb['pSegment'] . ";\n";
    return $ReturnJsonToWeb;
}
//Очистить всё
function ActionClearALL() {
    echo "Очистка экрана\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "Clear",
    ];
}
//Очистить Табло
function ActionClearTablo() {
    echo "Очистка экрана Табло\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "ClearTablo",
    ];
}
//Очистить Титры
function ActionClearTV() {
    echo "Очистка Титров\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "ClearTV",
    ];
}
//Очистить "Уголок слёз и поцелуев"
function ActionClearKissAndCry() {
    echo "Очистка 'Уголок слёз и поцелуев'\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "ClearKissAndCry",
    ];
}
//Очистить титры: Персональные данные
function ActionClearTVPersonal() {
    echo "Очистка экрана\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "ClearTVPersonal",
    ];
}
//Очистить титры: Группы
function ActionClearTVGroup() {
    echo "Очистка титры: Группы\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "ClearTVGroup",
    ];
}
//Очистить титры: Название соревнования (Segment)
function ActionClearTVSegment() {
    echo "Очистка титры: Название соревнования (Segment)\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "ClearTVSegment",
    ];
}
//Воспроизведение: Последняя минута разминки
function ActionVoiceOneMinute() {
    echo "Воспроизведение: Последняя минута разминки\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "VoiceOneMinute",
    ];
}
//Воспроизведение: Разминка завершена
function ActionVoiceWarmCompleted() {
    echo "Воспроизведение: Разминка завершена\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "VoiceWarmCompleted",
    ];
}
//Воспроизведение: Начало соревнования
function ActionVoiceStartGame() {
    echo "Воспроизведение: Начало соревнования\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "VoiceStartGame",
    ];
}
//Перезагрузить: Kiss&Cry
function ActionReloadKissAndCry() {
    echo "Перезагрузить: Kiss&Cry\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "ReloadKissAndCry",
    ];
}
//Перезагрузить: Табло
function ActionReloadTablo() {
    echo "Перезагрузить: Куб\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "ReloadTablo",
    ];
}
//Перезагрузить: Титры
function ActionReloadTV() {
    echo "Перезагрузить: Титры\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "ReloadTV",
    ];
}
// Воспроизведение: Последняя минута разминки
/*function ActionVoiceOneMinute() {
    echo "Перезагрузить: Титры\n";
    return [
        "timestamp"    => time(),
        "dAction"      => "VoiceOneMinute",
    ];
};*/

function FuncWorksCalc($data_line, $connection) {
    global $EventDB;
    global $users;
    global $timeOldCheckAction;
    $data_line = preg_replace('/[]/', '', $data_line);

    if (!empty($data_line)) {
        $xml_line = simplexml_load_string(mb_convert_encoding($data_line, "UTF-8", "cp1251"), 'SimpleXMLElement', LIBXML_NOCDATA);
        /**************** Наполняем базу 2 ********************************/
        $ReturnJsonToWeb = [];
        if (!$xml_line) { /* ---- */ }
        elseif (is_object($xml_line) and is_object($xml_line->Segment_Start) and is_object($xml_line->Segment_Start->Event)) {
            $EventDB = [
                'timestamp'        => time(),
                'ID'               => (int) $xml_line->Segment_Start->Event['ID'],
                'Name'             => (string) $xml_line->Segment_Start->Event['Name'],
                'CurrentSegmentID' => (int) $xml_line->Segment_Start["Segment_ID"],
                'IsuCalcVersion'   => (string) $xml_line["IsuCalcFs"],
                'DatabaseVersion'  => (int) $xml_line["Database"],
                'TimerAction'      => '',
                'Category'         => [],
                'Segment'          => [],
                'Judge'            => [],
                'Participants'     => [],
                'Criteria'         => [],
                'Deduction'        => [],
                'LiveTV'           => [],
                'ELS'              => [],
            ];
            if (is_object($xml_line->Segment_Start->Event->Event_Officials_List)) {
                foreach ($xml_line->Segment_Start->Event->Event_Officials_List->Official as $Official) {
                    $EventDB['Judge'][(int)$Official['Index']] = [
                        'pID'       => (int)$Official['ID'],
                        'pFullName' => (string)$Official['Full_Name'],
                        'pIndex'    => (int)$Official['Index'],
                        'pNation'   => (string)$Official['Nation'],
                        'pClub'     => (string)$Official['Club'],
                        'dFunction' => (string)$Official['Function'],
                    ];
                    //echo "-----------------\n";
                    //echo "Index: "    . $EventDB['Judge'][(int)$Official['Index']]['pIndex'] . ";\n";
                    //echo "FullName: " . $EventDB['Judge'][(int)$Official['Index']]['pFullName'] . ";\n";
                    //echo "Nation: "   . $EventDB['Judge'][(int)$Official['Index']]['pNation'] . ";\n";
                    //echo "Club: "     . $EventDB['Judge'][(int)$Official['Index']]['pClub'] . ";\n";
                    //echo "Function: " . $EventDB['Judge'][(int)$Official['Index']]['dFunction'] . ";\n";
                }
                ksort($EventDB['Judge']);
            }
            //необходимо очистить состояние, т.к. загружаем новую базу
            $timeOldCheckAction = -1;
            echo "\n\n".$EventDB['Name']."\n\n";
            if (is_object($xml_line->Segment_Start->Event->Category_List)) {
                foreach ($xml_line->Segment_Start->Event->Category_List->Category as $Category) {
                    $EventDB['Category'] = [
                        'ID'   => (int) $Category['ID'],
                        'Name' => (string) $Category['Name'],
                        'Type' => (string) $Category['Type'],
                    ];
                }
                if (is_object($xml_line->Segment_Start->Event->Category_List->Category->Segment_List)) {
                    foreach ($xml_line->Segment_Start->Event->Category_List->Category->Segment_List->Segment as $Segment) {
                        if ((int) $Segment['ID'] == $EventDB["CurrentSegmentID"]) {
                            $EventDB['Segment'] = [
                                'ID'           => (int) $Segment['ID'],
                                'Name'         => (string) $Segment['Name'],
                                'Abbreviation' => (string) $Segment['Abbreviation'],
                                'Type'         => (string) $Segment['Type'],
                                'Group'        => (string) $Segment['Group'],
                            ];
                            
                            if (is_object($Segment->Segment_Official_List)) {
                                foreach ($Segment->Segment_Official_List->Official as $Official) {
                                    $OfficialIndex = (int)$Official['Index'];
                                    $EventDB['Judge'][$OfficialIndex] = [
                                        'pID'       => (int)$Official['ID'],
                                        'pFullName' => mb_convert_case($Official['Full_Name'], MB_CASE_TITLE, "UTF-8"),
                                        'pIndex'    => (int)$Official['Index'],
                                        'pNation'   => (string)$Official['Nation'],
                                        'pClub'     => (string)$Official['Club'],
                                        'dFunction' => (string)$Official['Function'],
                                    ];
                                    //echo "Index: "    . $EventDB['Judge'][(int)$Official['Index']]['pIndex'] . ";\n";
                                    //echo "FullName: " . $EventDB['Judge'][(int)$Official['Index']]['pFullName'] . ";\n";
                                    //echo "Nation: "   . $EventDB['Judge'][(int)$Official['Index']]['pNation'] . ";\n";
                                    //echo "Club: "     . $EventDB['Judge'][(int)$Official['Index']]['pClub'] . ";\n";
                                    //echo "Function: " . $EventDB['Judge'][(int)$Official['Index']]['dFunction'] . ";\n";
                                }
                                ksort($EventDB['Judge']);
                            }
                            //Критерии (требования)
                            if (is_object($Segment->Criteria_List)) {
                                foreach ($Segment->Criteria_List->Criteria as $Criteria) {
                                    $EventDB['Criteria']["c".$Criteria['Index']] = [
                                        'Name'   => mb_convert_case($Criteria['Cri_Name'], MB_CASE_TITLE, "UTF-8"),
                                        'Abbrev' => (string)$Criteria['Cri_Abbrev'],
                                        'Factor' => (int)$Criteria['Cri_Factor'],
                                    ];
                                }
                            }
                            //Нарушения
                            if (is_object($Segment->Deduction_List)) {
                                foreach ($Segment->Deduction_List->Deduction as $Deduction) {
                                    $EventDB['Deduction']["d".$Deduction['Index']] = [
                                        'Name' => mb_convert_case($Deduction['Ded_Name'], MB_CASE_TITLE, "UTF-8"),
                                        'Edit' => (string)$Deduction['Ded_Edit'],
                                    ];
                                }
                            }
                        }
                    }
                }

                if (is_object($xml_line->Segment_Start->Event->Category_List->Category->Participant_List)) {
                    foreach ($xml_line->Segment_Start->Event->Category_List->Category->Participant_List->Participant as $Participant) {
                        foreach ($xml_line->Segment_Start->Event->Category_List->Category->Segment_List->Segment as $Segment) {
                            if ((int) $Segment['ID'] == (int) $xml_line->Segment_Start["Segment_ID"]) {
                                foreach ($Segment->Segment_Start_List->Performance as $Performance) {
                                    if ((int) $Participant['ID'] == (int) $Performance['ID']) {
                                        if (preg_match_all('/^(.*?),(.*?)$/', $Participant['Club'],$ClubAndCity)) {
                                            //print_r($ClubAndCity);
                                        }
                                        else {
                                            if (!empty($Participant['Club'])) {
                                                $ClubAndCity[1][0]=(string)$Participant['Club'];
                                                $ClubAndCity[2][0]='';
                                            }
                                            else {
                                                $ClubAndCity[1][0]='';
                                                $ClubAndCity[2][0]='';
                                            }
                                        }
                                        
                                        $EventDB['Participants']["p-".$Participant['ID']] = [
                                            'ID'          => (int)$Participant['ID'],
                                            'FullName'    => (string)$Participant['Full_Name'],
                                            'Nation'      => (string)$Participant['Nation'],
                                            'Club'        => (string)$ClubAndCity[2][0],
                                            'City'        => (string)$ClubAndCity[1][0],
                                            'Music'       => (empty($Participant['Music'])) ? ''  : (string)$Participant['Music'],
                                            'Coach'       => (empty($Participant['Coach'])) ? 'Нет тренера' : (string)$Participant['Coach'],
                                            'Status'      => (string)$Participant['Status'],
                                            //Сортировка за выступление
                                            'Sort'        => 0,
                                            //Место за текущее выступление
                                            'Rank'        => 0,
                                            //Баллы за текущее выступление
                                            'SeqPoints'   => '',
                                            //Баллы за элементы
                                            'TES'         => '',
                                            //Баллы за компоненты
                                            'TCS'         => '',
                                            //Общая сортировка соревнования
                                            'TSort'       => 0,
                                            //Итоговое место
                                            'TRank'       => 0,
                                            //Общее количество баллов
                                            'TPoint'      => '',
                                            'StartNumber' => (int)$Performance['Start_Number'],
                                            'GroupNumber' => (int)$Performance['Start_Group_Number'],
                                            'Bonus'       => 0,
                                            'DedSum'      => 0,
                                            'Element'     => [],
                                            'Deduction'   => [],
                                            'Criteria'    => [],
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //База пуста необходима загрузка из файла!!!
        elseif (is_object($xml_line->Segment_Running->Action) && empty($EventDB)) {
            $DBFileNew = json_decode( file_get_contents(__DIR__ . '/DB/DB.json') , true );
            if ($DBFileNew['CurrentSegmentID'] == (int) $xml_line->Segment_Running["Segment_ID"] && $DBFileNew['IsuCalcVersion'] == (string) $xml_line["IsuCalcFs"] && $DBFileNew['DatabaseVersion'] == (int) $xml_line["Database"]) {
                    echo "-----------------------------------------------------------------------------------------\n";
                    echo "Данных Загружены!!!\n".$DBFileNew['CurrentSegmentID'] . "=".$xml_line->Segment_Running['Segment_ID'];
                    echo "-----------------------------------------------------------------------------------------\n";
                    $EventDB = $DBFileNew;
            }
            else {
                echo "-----------------------------------------------------------------------------------------\n";
                echo "Данных нет!!!\nПерезагрузите Calc!!!\n".$DBFileNew['CurrentSegmentID'] . "=".$xml_line->Segment_Running['Segment_ID'];
                echo "-----------------------------------------------------------------------------------------\n";
            }
        }
        ##### Команды
        elseif (is_object($xml_line->Segment_Running->Action)) {
            $CommandAction = $xml_line->Segment_Running->Action['Command'];
            //Первая загрузка
            if ($CommandAction == 'INI') {
                echo "Загрузка данных\n";
                if(is_object($xml_line->Segment_Running->Segment_Result_List)) {
                    foreach ($xml_line->Segment_Running->Segment_Result_List->Performance as $Performance) {
                        $ParticipantID = "p-" . $Performance['ID'];
                        if (array_key_exists($ParticipantID,$EventDB['Participants'])) {
                            //Сортировка
                            $EventDB['Participants'][$ParticipantID]['Sort']      = (int)$Performance['Index'];
                            //Место
                            $EventDB['Participants'][$ParticipantID]['Rank']      = (int)$Performance['Rank'];
                            //Балы
                            $EventDB['Participants'][$ParticipantID]['SeqPoints'] = (string)$Performance['Points'];
                            //Балы за элементы
                            $EventDB['Participants'][$ParticipantID]['TES']       = (string)$Performance['TES'];
                            //Балы за компоненты
                            $EventDB['Participants'][$ParticipantID]['TCS']       = (string)$Performance['TCS'];
                            //Балы Бонус
                            $EventDB['Participants'][$ParticipantID]['Bonus']     = (string)$Performance['Bonus'];
                            //Балы Снижение
                            $EventDB['Participants'][$ParticipantID]['DedSum']    = (string)$Performance['Ded_Sum'];
                        }
                        unset($ParticipantID);
                    }
                }
                if(is_object($xml_line->Segment_Running->Category_Result_List)) {
                    foreach ($xml_line->Segment_Running->Category_Result_List->Participant as $Participant) {
                        $ParticipantID = "p-" . $Participant['ID'];
                        if (array_key_exists($ParticipantID,$EventDB['Participants'])) {
                            //Итоговая сортировка
                            $EventDB['Participants'][$ParticipantID]['TSort']  = (int)$Participant['TIndex'];
                            //Итоговое место
                            $EventDB['Participants'][$ParticipantID]['TRank']  = (int)$Participant['TRank'];
                            //Итоговые баллы
                            $EventDB['Participants'][$ParticipantID]['TPoint'] = (string)$Participant['TPoint'];
                        }
                        unset($ParticipantID);
                    }
                }
                $ReturnJsonToWeb = [
                    "timestamp"  => time(),
                    "dAction"    => "INIT",
                    "EventName"  => (string)$EventDB["Name"],
                    "pCategory"  => (string)$EventDB["Category"]["Name"],
                    "pSegment"   => (string)$EventDB["Segment"]["Name"],
                ];

                echo "---------------------------------------------------------------------\n";
                echo "Action: INIT;\n";
                echo "EventName: " . $ReturnJsonToWeb['EventName'] . ";\n";
                echo "CategoryName: " . $ReturnJsonToWeb['pCategory'] . ";\n";
                echo "SegmentName: " . $ReturnJsonToWeb['pSegment'] . ";\n";

            }
            //Обновление технической информации по данному выступлению
            elseif ($CommandAction == '1S1') {
                $ParticipantID = "p-" . $xml_line->Segment_Running->Action['Current_Participant_ID'];

                //Баллы за текущее выступление
                $EventDB['Participants'][$ParticipantID]['SeqPoints'] = (int)$xml_line->Segment_Running->Prf_Details['Points'];
                //Баллы за элементы
                $EventDB['Participants'][$ParticipantID]['TES']       = (string)$xml_line->Segment_Running->Prf_Details['TES'];
                //Баллы за компоненты
                $EventDB['Participants'][$ParticipantID]['TCS']       = (string)$xml_line->Segment_Running->Prf_Details['TCS'];
                //Баллы Бонус
                $EventDB['Participants'][$ParticipantID]['Bonus']     = (string)$xml_line->Segment_Running->Prf_Details['Bonus'];
                //Баллы Снижение
                $EventDB['Participants'][$ParticipantID]['DedSum']    = (string)$xml_line->Segment_Running->Prf_Details['Ded_Sum'];
                //Элементы
                if(is_object($xml_line->Segment_Running->Prf_Details->Element_List)) {
                    foreach ($xml_line->Segment_Running->Prf_Details->Element_List->Element as $Element) {
                        $ElementID = "e" . $Element['Index'];
                        //Сокращенное наименование элементов
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Name']     = (string)$Element['Elm_Name'];
                        //Баллы
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Points']   = (string)$Element['Points'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['BV']       = (string)$Element['Elm_XBV'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['GOE']      = (string)$Element['Elm_XGOE'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Info']     = (string)$Element['Elm_Info'];
                        //Элемент защитан или нет
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Half']     = (string)$Element['Elm_Half'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['BVWB']     = (string)$Element['Elm_XBVWB'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Review']   = (int)$Element['Elm_Review'];
                        //Полное наименование элементов
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Fullname'] = (string)$Element['Elm_Name_Long'];
                        unset($ElementID);
                    }
                    ksort($EventDB['Participants'][$ParticipantID]['Element']);
                }
                //Нарушения (Пока не знаю что за хрень)
                if(is_object($xml_line->Segment_Running->Prf_Details->Deduction_List)) {
                    foreach ($xml_line->Segment_Running->Prf_Details->Deduction_List->Deduction as $Deduction) {
                        $DeductionID = "d" . $Deduction['Index'];
                        //Название нарушения
                        $EventDB['Participants'][$ParticipantID]['Deduction'][$DeductionID]['Name']  = (string)$EventDB['Deduction']['d'.$Deduction['Index']]['Name'];
                        //Баллы
                        $EventDB['Participants'][$ParticipantID]['Deduction'][$DeductionID]['Value'] = (string)$Deduction['Ded_Value'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Deduction'][$DeductionID]['Count'] = (string)$Deduction['Ded_Count'];
                        unset($DeductionID);
                    }
                    ksort($EventDB['Participants'][$ParticipantID]['Deduction']);
                }
                //Критерии (Пока не знаю что за хрень)
                if(is_object($xml_line->Segment_Running->Prf_Details->Criteria_List)) {
                    foreach ($xml_line->Segment_Running->Prf_Details->Criteria_List->Criteria as $Criteria) {
                        $CriteriaID = "c" . $Criteria['Index'];
                        //Сокращенное наименование элементов
                        $EventDB['Participants'][$ParticipantID]['Criteria'][$CriteriaID]['Name']   = (string)$EventDB['Criteria']['c'.$Criteria['Index']]['Name'];
                        //Баллы
                        $EventDB['Participants'][$ParticipantID]['Criteria'][$CriteriaID]['Points'] = (string)$Criteria['Points'];
                        unset($CriteriaID);
                    }
                    ksort($EventDB['Participants'][$ParticipantID]['Criteria']);
                }
                unset($ParticipantID);
                echo "---------------------------------------------------------------------\n";
                echo "Action: 1S1;\n";
            }
            //Обновление общих результатов текущего выступления
            elseif ($CommandAction == '1S2') {
                if(is_object($xml_line->Segment_Running->Segment_Result_List)) {
                    foreach ($xml_line->Segment_Running->Segment_Result_List->Performance as $Performance) {
                        $ParticipantID = "p-" . $Performance['ID'];
                        //Сортировка
                        $EventDB['Participants'][$ParticipantID]['Sort']      = (int)$Performance['Index'];
                        //Если участник отсутствует
                        if ($Performance['Status'] != "OK") {
                            //Место
                            $EventDB['Participants'][$ParticipantID]['Rank']      = 0;
                            //Балы за прокат
                            $EventDB['Participants'][$ParticipantID]['SeqPoints'] = '';
                        }
                        else {
                            //Место
                            $EventDB['Participants'][$ParticipantID]['Rank']      = (int)$Performance['Rank'];
                            //Балы за прокат
                            $EventDB['Participants'][$ParticipantID]['SeqPoints'] = (string)$Performance['Points'];
                        }
                        unset($ParticipantID);
                    }
                }
                echo "---------------------------------------------------------------------\n";
                echo "Action: 1S2;\n";
            }
            //Обновление общих результатов всего соревнования
            elseif ($CommandAction == '1S3') {
                if(is_object($xml_line->Segment_Running->Category_Result_List)) {
                    foreach ($xml_line->Segment_Running->Category_Result_List->Participant as $Participant) {
                        $ParticipantID = "p-" . $Participant['ID'];
                        if (array_key_exists($ParticipantID,$EventDB['Participants'])) {
                            //Итоговая сортировка
                            $EventDB['Participants'][$ParticipantID]['TSort'] = (int)$Participant['TIndex'];
                            //Если участник отсутствует
                            if ($Participant['Status'] != "ACT") {
                                //Итоговое место
                                $EventDB['Participants'][$ParticipantID]['TRank']  = 0;
                                //Итоговое количество баллов
                                $EventDB['Participants'][$ParticipantID]['TPoint'] = '';
                            }
                            else {
                                //Итоговое место
                                $EventDB['Participants'][$ParticipantID]['TRank']  = (int)$Participant['TRank'];
                                //Итоговое количество баллов
                                $EventDB['Participants'][$ParticipantID]['TPoint'] = (string)$Participant['TPoint'];
                            }
                        }
                        unset($ParticipantID);
                    }
                }
                echo "---------------------------------------------------------------------\n";
                echo "Action: 1S3;\n";
            }
            //Обновление ХЗ
            elseif ($CommandAction == '1S4') {
                if(is_object($xml_line->Segment_Running->Segment_Start_List)) {
                    foreach ($xml_line->Segment_Running->Segment_Start_List->Performance as $Performance) {
                        $ParticipantID = "p-" . $Performance['ID'];
                        //Итоговая сортировка
                        //$EventDB['Participants'][$ParticipantID]['TSort']  = $Performance['TIndex'];
                        //Если участник отсутствует
                        if ($Performance['Status'] != "OK") {
                            //Итоговое место
                            //$EventDB['Participants'][$ParticipantID]['TRank']  = 0;
                            //Итоговое количество баллов
                            //$EventDB['Participants'][$ParticipantID]['TPoint'] = 0;
                        }
                        else {
                            //Итоговое место
                            //$EventDB['Participants'][$ParticipantID]['TRank']  = $Performance['TRank'];
                            //Итоговое количество баллов
                            //$EventDB['Participants'][$ParticipantID]['TPoint'] = $Performance['TPoint'];
                        }

                        unset($ParticipantID);
                    }
                }
                echo "---------------------------------------------------------------------\n";
                echo "Action: 1S4;\n";
            }
            //Показать технические результаты проката
            elseif ($CommandAction == '1SC') {
                $ReturnJsonToWeb = ActionPersonalResult('1SC', "p-" . $xml_line->Segment_Running->Action['Current_Participant_ID']);
            }
            //Показать индивидуальные результаты проката
            elseif ($CommandAction == '2SC') {
                $ReturnJsonToWeb = ActionPersonalResult('2SC', "p-" . $xml_line->Segment_Running->Action['Current_Participant_ID']);
            }
            //Информация об участнике
            elseif ($CommandAction == 'NAM') {
                $ReturnJsonToWeb = ActionPersonalResult('NAM', "p-" . $xml_line->Segment_Running->Action['Current_Participant_ID']);
            }
            //Очистка экрана
            elseif ($CommandAction == 'CLR' or $CommandAction == 'STP') {
                $ReturnJsonToWeb = ActionClearAll();
            }
            //Информация об судьях
            elseif ($CommandAction == 'JDG') {
                $ReturnJsonToWeb = ActionJudge((int)$xml_line->Segment_Running->Action['Judge_ID']);
            }
            //Start List (STL) Стартовый лист
            elseif ($CommandAction == 'STL') {
                $ReturnJsonToWeb = ActionGroup('STL',0);
            }
            //Warm Group (WUP) Список группы разминки
            elseif ($CommandAction == 'WUP') {
                $ReturnJsonToWeb = ActionGroup('WUP',(int)$xml_line->Segment_Running->Action['Current_Participant_ID']);
            }
            //3nd Score (3SC) Список промежуточных результатов соревнования
            elseif ($CommandAction == '3SC') {
                $ReturnJsonToWeb = ActionGroup('3SC', (int)$xml_line->Segment_Running->Action['Current_Participant_ID']);
            }
            // 
            elseif ($CommandAction == 'IRS') {
                $ReturnJsonToWeb = ActionGroup('IRS', (int)$xml_line->Segment_Running->Action['Current_Participant_ID']);
            }
            // 
            elseif ($CommandAction == 'RES') {
                $ReturnJsonToWeb = ActionGroup('RES', (int)$xml_line->Segment_Running->Action['Current_Participant_ID']);
            }
            //Victory Ceremony (VTR) Церемония награждения
            elseif ($CommandAction == 'VTR') {
                $ReturnJsonToWeb = ActionVictory($xml_line->Segment_Running->Action['Sub_Command']);
            }
            //Segment (SEG) Название соревнования
            elseif ($CommandAction == 'SEG') {
                $ReturnJsonToWeb = ActionSegment();
            }
            // Time+ Таймер старт 
            elseif ($CommandAction == 'TFW') {
                $EventDB["TimerAction"] = 'TimerStart';
                $ReturnJsonToWeb = [
                    "timestamp"  => time(),
                    "dAction"    => "TimerStart",
                    "Time"       => (string)$xml_line->Segment_Running->Action['Running_Time'],
                    "TimerState" => (string)$xml_line->Segment_Running->Action['Clock_State'],
                ];

                echo "---------------------------------------------------------------------\n";
                echo "Action: Timer Start;\n";
                echo "Time: " . $ReturnJsonToWeb['Time'] . ";  " . "Timer State: " . $ReturnJsonToWeb['TimerState'] . ";\n";
            }
            // Time- Таймер отсчет
            elseif ($CommandAction == 'TBW') {
                $EventDB["TimerAction"] = 'TimerCountdown';
                $ReturnJsonToWeb = [
                    "timestamp"  => time(),
                    "dAction"    => "TimerCountdown",
                    "Time"       => (string)$xml_line->Segment_Running->Action['Running_Time'],
                    "TimerState" => (string)$xml_line->Segment_Running->Action['Clock_State'],
                ];

                echo "---------------------------------------------------------------------\n";
                echo "Action: Timer Countdown;\n";
                echo "Time: " . $ReturnJsonToWeb['Time'] . ";  " . "Timer State: " . $ReturnJsonToWeb['TimerState'] . ";\n";
            }
            // Timer update (TIM) Таймер изменен
            elseif ($CommandAction == 'TIM') {
                if ($EventDB["TimerAction"] == '' && $xml_line->Segment_Running->Action['Clock_State'] == 1) {
                    if ($timeOldCheckAction == -1) {
                        $timeOldCheckAction = (float)$xml_line->Segment_Running->Action['Running_Time'];
                    }
                    elseif ((float)$xml_line->Segment_Running->Action['Running_Time'] > $timeOldCheckAction && $xml_line->Segment_Running->Action['Clock_State'] == 1) {
                        $EventDB["TimerAction"] = 'TimerStart';
                        echo "Action: Timer-Status-Empty TimerStart;\n";
                        $timeOldCheckAction = -1;
                    }
                    elseif ((float)$xml_line->Segment_Running->Action['Running_Time'] < $timeOldCheckAction && $xml_line->Segment_Running->Action['Clock_State'] == 1) {
                        $EventDB["TimerAction"] = 'TimerCountdown';
                        echo "Action: Timer-Status-Empty TimerCountdown;\n";
                        $timeOldCheckAction = -1;
                    }
                }
                elseif ($xml_line->Segment_Running->Action['Clock_State'] == 4) {
                    $EventDB["TimerAction"] = '';
                    $timeOldCheckAction = -1;
                }
                
                $ReturnJsonToWeb = [
                    "timestamp"  => time(),
                    "dAction"    => "Timer",
                    "sAction"    => $EventDB["TimerAction"],
                    "Time"       => (string)$xml_line->Segment_Running->Action['Running_Time'],
                    "TimerState" => (string)$xml_line->Segment_Running->Action['Clock_State'],
                ];

                echo "---------------------------------------------------------------------\n";
                echo "Action: Timer;\n";
                echo "Time: " . $ReturnJsonToWeb['Time'] . ";  " . "Timer State: " . $ReturnJsonToWeb['TimerState'] . "; TimerAction:" . $EventDB["TimerAction"] . ";\n";
            }
            // Timer pause (TPA) Таймер пауза
            elseif ($CommandAction == 'TPA') {
                $ReturnJsonToWeb = [
                    "timestamp" => time(),
                    "dAction"   => "TimerPause",
                    "Time"      => (string)$xml_line->Segment_Running->Action['Running_Time'],
                    "TimerState" => (string)$xml_line->Segment_Running->Action['Clock_State'],
                ];

                echo "---------------------------------------------------------------------\n";
                echo "Action: Timer Pause;\n";
                echo "Time: " . $ReturnJsonToWeb['Time'] . ";  " . "Timer State: " . $ReturnJsonToWeb['TimerState'] . ";\n";
            }
            // Timer stop (TST) Таймер стоп
            elseif ($CommandAction == 'TST') {
                $EventDB["TimerAction"] = '';
                $ReturnJsonToWeb = [
                    "timestamp" => time(),
                    "dAction"   => "TimerStop",
                    "Time"      => (string)$xml_line->Segment_Running->Action['Running_Time'],
                    "TimerState" => (string)$xml_line->Segment_Running->Action['Clock_State'],
                ];

                echo "---------------------------------------------------------------------\n";
                echo "Action: Timer Stop;\n";
                echo "Time: " . $ReturnJsonToWeb['Time'] . ";  " . "Timer State: " . $ReturnJsonToWeb['TimerState'] . ";\n";
            }
            // Таймер очистить
            elseif ($CommandAction == 'TCL') {
                $EventDB["TimerAction"] = '';
                $ReturnJsonToWeb = [
                    "timestamp" => time(),
                    "dAction"   => "TimerClear",
                    "Time"      => (string)$xml_line->Segment_Running->Action['Running_Time'],
                    "TimerState" => (string)$xml_line->Segment_Running->Action['Clock_State'],
                ];

                echo "---------------------------------------------------------------------\n";
                echo "Action: Timer Clear;\n";
                echo "Time: " . $ReturnJsonToWeb['Time'] . ";  " . "Timer State: " . $ReturnJsonToWeb['TimerState'] . ";\n";
            }
            // Элементы для ТВ, баллы
            elseif ($CommandAction == 'LTV') {
                if ($EventDB['LiveTV'] && $EventDB["TimerAction"] == "TimerStart" && 
                    ($EventDB['LiveTV']['TES']        != (string)$xml_line->Segment_Running->Prf_Details['TES'] || 
                     $EventDB['LiveTV']['TES_Leader'] != (string)$xml_line->Segment_Running->Prf_Details['TES_Leader'])
                   ) {
                    $EventDB['LiveTV'] = [
                        "TES"                => (string)$xml_line->Segment_Running->Prf_Details['TES'],
                        "TES_Leader"         => (string)$xml_line->Segment_Running->Prf_Details['TES_Leader'],
                        "TES_Leader_Overall" => (string)$xml_line->Segment_Running->Prf_Details['TES_Leader_Overall'],
                    ];
                    $ReturnJsonToWeb = [
                        "timestamp"  => time(),
                        "dAction"    => "LiveTV",
                        "TES"        => $EventDB['LiveTV']['TES'],
                        "TESLeader" => $EventDB['LiveTV']['TES_Leader'],
                    ];
    
                    echo "---------------------------------------------------------------------\n";
                    echo "Action: LiveTV;\n";
                    echo "TES: " . $ReturnJsonToWeb['TES'] . ";\n";
                    echo "TES Leader: " . $ReturnJsonToWeb['TESLeader'] . ";\n";
                }
            }
            // Элементы для ТВ, элементы
            elseif ($CommandAction == 'ELS') {
                $ParticipantID = (int)$xml_line->Segment_Running->Action['Current_Participant_ID'];
                $ReturnJsonToWeb = [
                    "timestamp" => time(),
                    "dAction"   => "LiveTV",
                    //Баллы за текущее выступление
                    "Points"    => (string)$xml_line->Segment_Running->Prf_Details['Points'],
                    //Баллы за элементы
                    "TES"       => (string)$xml_line->Segment_Running->Prf_Details['TES'],
                    //Баллы за компоненты
                    "TCS"       => (string)$xml_line->Segment_Running->Prf_Details['TCS'],
                    //Баллы Бонус
                    "Bonus"     => (string)$xml_line->Segment_Running->Prf_Details['Bonus'],
                    //Баллы Снижение
                    "Ded_Sum"   => (string)$xml_line->Segment_Running->Prf_Details['Ded_Sum'],
                ];
                //Элементы
                if(is_object($xml_line->Segment_Running->Prf_Details->Element_List)) {
                    foreach ($xml_line->Segment_Running->Prf_Details->Element_List->Element as $Element) {
                        $ElementID = (int)$Element['Index'];
                        //Сокращенное наименование элементов
                        $ReturnJsonToWeb['Element'][$ElementID]['Name']     = (string)$Element['Elm_Name'];
                        //Баллы
                        $ReturnJsonToWeb['Element'][$ElementID]['Points']   = (string)$Element['Points'];
                        //
                        $ReturnJsonToWeb['Element'][$ElementID]['BV']       = (string)$Element['Elm_XBV'];
                        //
                        $ReturnJsonToWeb['Element'][$ElementID]['GOE']      = (string)$Element['Elm_XGOE'];
                        //
                        $ReturnJsonToWeb['Element'][$ElementID]['Info']     = (string)$Element['Elm_Info'];
                        //Элемент защитан или нет
                        $ReturnJsonToWeb['Element'][$ElementID]['Half']     = (string)$Element['Elm_Half'];
                        //
                        $ReturnJsonToWeb['Element'][$ElementID]['BVWB']     = (string)$Element['Elm_XBVWB'];
                        //
                        $ReturnJsonToWeb['Element'][$ElementID]['Review']   = (int)$Element['Elm_Review'];
                        //Полное наименование элементов
                        $ReturnJsonToWeb['Element'][$ElementID]['Fullname'] = (string)$Element['Elm_Name_Long'];
                        unset($ElementID);
                    }
                    if (array_key_exists('Element', $ReturnJsonToWeb)) {
                        ksort($ReturnJsonToWeb['Element']);
                    }
                }
                //Нарушения
                if(is_object($xml_line->Segment_Running->Prf_Details->Deduction_List)) {
                    foreach ($xml_line->Segment_Running->Prf_Details->Deduction_List->Deduction as $Deduction) {
                        $DeductionID = (int)$Deduction['Index'];
                        //Название нарушения
                        $ReturnJsonToWeb['Deduction'][$DeductionID]['Name']  = (string)$EventDB['Deduction']['d'.$Deduction['Index']]['Name'];
                        //Баллы
                        $ReturnJsonToWeb['Deduction'][$DeductionID]['Value'] = (string)$Deduction['Ded_Value'];
                        unset($DeductionID);
                    }
                    if (array_key_exists('Deduction', $ReturnJsonToWeb)) {
                        ksort($ReturnJsonToWeb['Deduction']);
                    }
                }
                //Критерии (Пока не знаю что за хрень)
                if(is_object($xml_line->Segment_Running->Prf_Details->Criteria_List)) {
                    foreach ($xml_line->Segment_Running->Prf_Details->Criteria_List->Criteria as $Criteria) {
                        $CriteriaID = (int)$Criteria['Index'];
                        //Сокращенное наименование элементов
                        $ReturnJsonToWeb['Criteria'][$CriteriaID]['Name']   = (string)$EventDB['Criteria']['c'.$Criteria['Index']]['Name'];
                        //Баллы
                        $ReturnJsonToWeb['Criteria'][$CriteriaID]['Points'] = (string)$Criteria['Points'];
                        unset($CriteriaID);
                    }
                    if (array_key_exists('Criteria', $ReturnJsonToWeb)) {
                        ksort($ReturnJsonToWeb['Criteria']);
                    }
                }
                unset($ParticipantID);

                echo "---------------------------------------------------------------------\n";
                echo "Action: LiveTV(ELS);\n";
                echo "TES: " . $ReturnJsonToWeb['TES'] . ";\n";
                //echo "TES_Leader: " . $ReturnJsonToWeb['TES_Leader'] . ";\n";
                //echo "TES_Leader_Overall: " . $ReturnJsonToWeb['TES_Leader_Overall'] . ";\n";
            }
        }

        if (array_key_exists('dAction', $ReturnJsonToWeb)) {
            foreach($users as $connection) {
                $connection['connect']->send(json_encode($ReturnJsonToWeb));
            }
        }
        if ($xml_line->Segment_Running->Action['Command'] != 'TIM') {
            $DBFile = fopen(__DIR__ . '/DB/DB.json', 'w');
            fwrite($DBFile, json_encode($EventDB, JSON_PRETTY_PRINT|JSON_HEX_APOS|JSON_HEX_QUOT));
            fclose($DBFile);
        }

        empty($ReturnJsonToWeb);
    }
    return 1;
}

if ($ini['WriteRawInputCalc'] == "y") {
    $RawInputLogFile = fopen(__DIR__ . '/logs/RawInput-' . date('Y-m-d') . '-' . rand() . '.log', 'w');
}

require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

$ws_worker = new Worker("websocket://0.0.0.0:" . $ini["WebSocketPort"]);

// Тут храним пользовательские соединения
$users = [];
// Тут храним пользовательские соединения
$users2 = [];

$ws_worker->onConnect = function($connection) use (&$users, &$ini) {
    $connection->onWebSocketConnect = function($connection) use (&$users, &$ini) {
        $users[$connection->id]['connect'] = $connection;
        $RemoteIP = (string)$connection->getRemoteIp();
        if (array_key_exists($RemoteIP, $ini)) {
            if ($ini[$RemoteIP] != "") {
                $users[$connection->id]['admin'] = 1;
                $users[$connection->id]['role']  = [];
                foreach(explode(",", $ini[$RemoteIP]) as $val) {
                    array_push($users[$connection->id]['role'], trim($val));
                }
                if ($ini["PrintConsoleInfo"] == 1) {echo "Пользователь Администратор\n";}
            }
            else {
                $users[$connection->id]['admin'] = 0;
                echo "Пользователь НЕ Администратор\n";
            }
        }
        else {
            $users[$connection->id]['admin'] = 0;
            echo "Пользователь НЕ Администратор\n";
        }
    };
    echo "Клиент WebSocket Подключился, с IP:" . $connection->getRemoteIp() . "\n";
};

$ws_worker->onMessage = function($connection, $data) use (&$users, &$ini, &$EventDB) {
    if ($data == "INIT" && isset($EventDB["Name"])) {
        if (is_object($users[$connection->id]['connect'])) {
            $ReturnJsonToWeb = [
                "timestamp"   => time(),
                "dAction"     => "INIT",
                "EventName"   => $EventDB["Name"],
                "pCategory"   => $EventDB["Category"]["Name"],
                "pSegment"    => $EventDB["Segment"]["Name"],
                'TimerAction' => $EventDB["TimerAction"],
            ];

            echo "---------------------------------------------------------------------\n";
            echo "Action: INIT;\n";
            echo "EventName: " . $ReturnJsonToWeb['EventName'] . ";\n";
            echo "CategoryName: " . $ReturnJsonToWeb['pCategory'] . ";\n";
            echo "SegmentName: " . $ReturnJsonToWeb['pSegment'] . ";\n";
            echo "TimerAction: " . $ReturnJsonToWeb['TimerAction'] . ";\n";

            $users[$connection->id]['connect']->send(json_encode($ReturnJsonToWeb));
            echo "Отправка\n";
        }
        
    }
    elseif ($users[$connection->id]['admin'] == 1) {
        echo "ADMIN ACTION Ready\n";
        $ReturnJsonToWeb = '';
        if (in_array('All', $users[$connection->id]['role'], true)) {
            $AllRight = true;
            echo "---------------------------------------------------------------------\n";
            echo "У пользователя полные права\n";
        }
        else {
            $AllRight = false;
        }

        if (in_array('None', $users[$connection->id]['role'], true)) {
            echo "---------------------------------------------------------------------\n";
            echo "У пользователя нет никаких прав\n";
            $ReturnJsonToWeb = '';
        }
        elseif ($data == "Name" && ($AllRight || false !== array_search('Name', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: Name\n";
        }
        elseif ($data == "Segment" && ($AllRight || false !== array_search('Segment', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: Segment\n";
            $ReturnJsonToWeb = ActionSegment();
        }
        //Очистить всё
        elseif ($data == "Clear" && ($AllRight || false !== array_search('Clear', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: Clear All\n";
            $ReturnJsonToWeb = ActionClearAll();
        }
        //Очистить Табло
        elseif ($data == "ClearTablo" && ($AllRight || false !== array_search('ClearTablo', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: Clear Tablo\n";
            $ReturnJsonToWeb = ActionClearTablo();
        }
        //Очистить Титры
        elseif ($data == "ClearTV" && ($AllRight || false !== array_search('ClearTV', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION Clear TV\n";
            $ReturnJsonToWeb = ActionClearTV();
        }
        //Очистить "Уголок слёз и поцелуев"
        elseif ($data == "ClearKissAndCry" && ($AllRight || false !== array_search('ClearKissAndCry', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: Clear Kiss&Cry\n";
            $ReturnJsonToWeb = ActionClearKissAndCry();
        }
        elseif ($data == "ResultPersonal" && ($AllRight || false !== array_search('ResultPersonal', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: ResultPersonal\n";
        }
        elseif ($data == "ResultAll" && ($AllRight || false !== array_search('ResultAll', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: ResultAll\n";
        }
        elseif ($data == "StartList" && ($AllRight || false !== array_search('StartList', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: StartList\n";
            $ReturnJsonToWeb = ActionGroup('STL',0);
        }
        elseif ($data == "WarmGroup" && ($AllRight || false !== array_search('WarmGroup', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: WarmGroup\n";
        }
        elseif ($data == "JudgeAll" && ($AllRight || false !== array_search('JudgeAll', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: JudgeAll\n";
            $ReturnJsonToWeb = ActionJudge(-1);
        }
        //Воспроизведение: Последняя минута разминки
        elseif ($data == "VoiceOneMinute" && ($AllRight || false !== array_search('VoiceOneMinute', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: VoiceOneMinute\n";
            $ReturnJsonToWeb = ActionVoiceOneMinute();
        }
        //Воспроизведение: Разминка завершена
        elseif ($data == "VoiceWarmCompleted" && ($AllRight || false !== array_search('VoiceWarmCompleted', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: VoiceWarmCompleted\n";
            $ReturnJsonToWeb = ActionVoiceWarmCompleted();
        }
        //Воспроизведение: Начало соревнования
        elseif ($data == "VoiceStartGame" && ($AllRight || false !== array_search('VoiceStartGame', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: VoiceStartGame\n";
            $ReturnJsonToWeb = ActionVoiceStartGame();
        }
        //Перезагрузка "Уголок слёз и поцелуев"
        elseif ($data == "ReloadKissAndCry" && ($AllRight || false !== array_search('ReloadKissAndCry', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: ReloadKissAndCry\n";
            $ReturnJsonToWeb = ActionReloadKissAndCry();
        }
        //Перезагрузка табло
        elseif ($data == "ReloadTablo" && ($AllRight || false !== array_search('ReloadTablo', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: ReloadTablo\n";
            $ReturnJsonToWeb = ActionReloadTablo();
        }
        //Перезагрузка титров
        elseif ($data == "ReloadTV" && ($AllRight || false !== array_search('ReloadTV', $users[$connection->id]['role']))) {
            echo "---------------------------------------------------------------------\n";
            echo "ADMIN ACTION: Reload TV\n";
            $ReturnJsonToWeb = ActionReloadTV();
        }
        //Перезагрузка конфиг. файла
        elseif ($data == "ReOpenINI") {
            echo "Action: ReOpenINI\n";
            // Обрабатываем конфигурационный файл по-умолчанию: config-default.ini
            $configDefault = parse_ini_file(__DIR__ . "/config-default.ini");
            // Обрабатываем локальный конфигурационный файл: config-local.ini
            if (file_exists(__DIR__ . "/config-local.ini")) {
                $configLocal = parse_ini_file(__DIR__ . "/config-local.ini");
                $ini = array_merge($configDefault, $configLocal);
                unset($configLocal);
            }
            else {
                $ini = $configDefault;
            }

            unset($configDefault);

            if (!is_array($ini)) {
                print_r($ini);
                echo "Не удалось прочитать конфигурационный файл.\n";
                exit;
            }
        }
        else {
            echo "У пользователя нет прав на выполнение данной команды или нет такой команды!\n";
        }
        if ($ReturnJsonToWeb != '') {
            if (array_key_exists('dAction', $ReturnJsonToWeb)) {
                foreach($users as $connection) {
                    $connection['connect']->send(json_encode($ReturnJsonToWeb));
                }
            }
            $ReturnJsonToWeb = '';
        }
    }
};

$ws_worker->onClose = function($connection) use(&$users) {
    // unset parameter when user is disconnected
    unset($users[$connection->id]);
    echo "Клиент WebSocket Отключился, с IP:" . $connection->getRemoteIp() . "\n";
};

// it starts once when you start server.php:
$ws_worker->onWorkerStart = function() use (&$users, &$users2, &$ini) {

    $ws_worker2 = new Worker("tcp://" . $ini['PROXY_CALC_IP'] . ":". $ini['PROXY_CALC_PORT']);

    $ws_worker2->onConnect = function($connection) use (&$users2, &$ini) {
        $users2[$connection->id]['connect'] = $connection;
        echo "Клиент подключился, с IP:" . $connection->getRemoteIp() . "\n";
    };

    $ws_worker2->onClose = function($connection) use(&$users2) {
        // unset parameter when user is disconnected
        unset($users2[$connection->id]);
        echo "Клиент Отключился, с IP:" . $connection->getRemoteIp() . "\n";
    };
    $ws_worker2->listen();


    $connection = new AsyncTcpConnection("tcp://" . $ini['CALC_IP'] . ":". $ini['CALC_PORT']);
    $connection->maxSendBufferSize = 4*1024*1024;
    $connection->onConnect = function($connection) {
        echo "Мы подключились к Calc!\n";
    };
    $EventDB = [];
    $stop = 1;
    $NewData = '';
    $connection->onMessage = function($connection, $data) use (&$users, &$users2, &$ini) {
        global $NewData;
        global $RawInputLogFile;
        /*foreach($users2 as $connection2) {
            $connection2['connect']->send($data);
        }*/
        if ($ini['WriteRawInputCalc'] == "y") {
            fwrite($RawInputLogFile, $data);
        }
        $stopSimbol = ord(substr($data, -1));
        if ($stopSimbol == 3 && $NewData == '') {
            $NewData = $data;
        }
        elseif ($stopSimbol == 3 && $NewData != '') {
            $NewData .= $data;
        }
        else {
            $NewData .= $data;
        }

        if ($stopSimbol == 3) {
            foreach (explode(chr(3), $NewData) as $data_line1) {
                $startSimbol = ord(substr($data_line1, 0));
                if ($startSimbol == 2) {
                    FuncWorksCalc(ltrim($data_line1), $connection);
                }
            }
            $NewData = '';
            //unset($NewData);
        }
    };
    $connection->onClose = function($connection) {
        echo "Отключились от Calc. Подключаемся повторно через 5 секунд.\n";
        // Подключаемся повторно через 5 секунд
        $connection->reConnect(5);
    };
    $connection->connect();
};






// Run worker
Worker::runAll();