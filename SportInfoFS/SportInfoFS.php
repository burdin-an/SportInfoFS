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
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

setlocale(LC_CTYPE, 'ru_RU.UTF-8');
error_reporting(E_ALL ^ E_WARNING);

// Обрабатываем конфигурационный файл: config.ini
$ini = parse_ini_file(__DIR__ . "/config.ini");
if (!is_array($ini)) {
    print_r($ini);
    echo "Неудалось прочитать конфигурационный файл.\n";
    exit;
}

$EventDB = [];
$timeOldCheckAction = -1;

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
            ];
            if (is_object($xml_line->Segment_Start->Event->Event_Officials_List)) {
                foreach ($xml_line->Segment_Start->Event->Event_Officials_List->Official as $Official) {
                    $EventDB['Judge'][(int)$Official['Index']] = [
                        'pID'       => (int)$Official['ID'],
                        'pFullName' => (string)$Official['Full_Name'],
                        'pIndex'    => (int)$Official['Index'],
                        'pNation'   => (string)$Official['Nation'],
                        'pClub'     => (string)(empty($Official['Club'])) ?  '' : preg_replace('/^(.*),(.*)/', '\1', $Official['Club']),
                        'dFunction' => (string)$Official['Function'],
                    ];
                }
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
                            ];
                            
                            if (is_object($Segment->Segment_Official_List)) {
                                foreach ($Segment->Segment_Official_List->Official as $Official) {
                                    $OfficialIndex = (int)$Official['Index'];
                                    $EventDB['Judge'][$OfficialIndex] = [
                                        'pID'       => (int)$Official['ID'],
                                        'pFullName' => mb_convert_case($Official['Full_Name'], MB_CASE_TITLE, "UTF-8"),
                                        'pIndex'    => (int)$Official['Index'],
                                        'pNation'   => (string)$Official['Nation'],
                                        'pClub'     => (string)(empty($Official['Club'])) ?  '' : preg_replace('/^(.*),(.*)/', '\1', $Official['Club']),
                                        'dFunction' => (string)$Official['Function'],
                                    ];
                                }
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
                                        'Name'   => mb_convert_case($Deduction['Ded_Name'], MB_CASE_TITLE, "UTF-8"),
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
                                        $EventDB['Participants']["p-".$Participant['ID']] = [
                                            'ID'          => (int)$Participant['ID'],
                                            'SegmentID'   => 0,
                                            'FullName'    => mb_convert_case($Participant['Full_Name'], MB_CASE_TITLE, "UTF-8"),
                                            'Club'        => $Participant['Club'],
                                            'Nation'      => (empty($Participant['Club'])) ?  '' : preg_replace('/^(.*),(.*)/', '\1', $Participant['Club']),//$Participant['Nation']
                                            //'Club'        => (empty($Participant['Club'])) ?  '' : preg_replace('/^(.*),(.*)/', '\2', $Participant['Club']),
                                            'Music'       => (empty($Participant['Music'])) ? '' : $Participant['Music'],
                                            'Coach'       => (empty($Participant['Coach'])) ? 'Нет тренера' : $Participant['Coach'],
                                            'Status'      => $Participant['Status'],
                                            //Сортировка за выступление
                                            'Sort'        => 0,
                                            //Место за текущее выступление
                                            'Rank'        => 0,
                                            //Баллы за текущее выступление
                                            'SeqPoints'   => 0,
                                            //Баллы за элементы
                                            'TES'         => 0,
                                            //Баллы за компоненты
                                            'TCS'         => 0,
                                            //Общая сортировка серевнования
                                            'TSort'       => 0,
                                            //Итоговое место
                                            'TRank'       => 0,
                                            //Общее количество баллов
                                            'TPoints'     => 0,
                                            'StartNumber' => (int)$Performance['Start_Number'],
                                            'GroupNumber' => (int)$Performance['Start_Group_Number'],
                                            'Bonus'       => 0,
                                            'DedSum'      => 0,
                                            'Element'     => [],
                                            'Criteria'    => [],
                                            'Deduction'   => [],
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
            $DBFileNew = json_decode( file_get_contents(__DIR__ . '/config/DB.json') , true );
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
                        //Сортировка
                        $EventDB['Participants']["p-".$Performance['ID']]['Sort']      = $Performance['Index'];
                        //Место
                        $EventDB['Participants']["p-".$Performance['ID']]['Rank']      = $Performance['Rank'];
                        //Балы
                        $EventDB['Participants']["p-".$Performance['ID']]['SeqPoints'] = $Performance['Points'];
                        //Балы за элементы
                        $EventDB['Participants']["p-".$Performance['ID']]['TES']       = $Performance['TES'];
                        //Балы за компоненты
                        $EventDB['Participants']["p-".$Performance['ID']]['TCS']       = $Performance['TCS'];
                        //Балы Бонус
                        $EventDB['Participants']["p-".$Performance['ID']]['Bonus']     = $Performance['Bonus'];
                        //Балы Снижение
                        $EventDB['Participants']["p-".$Performance['ID']]['DedSum']    = $Performance['Ded_Sum'];
                    }
                }
                if(is_object($xml_line->Segment_Running->Category_Result_List)) {
                    foreach ($xml_line->Segment_Running->Category_Result_List->Participant as $Participant) {
                        //Итоговая сортировка
                        $EventDB['Participants']["p-".$Participant['ID']]['TSort']   = $Participant['TIndex'];
                        //Итоговое место
                        $EventDB['Participants']["p-".$Participant['ID']]['TRank']   = $Participant['TRank'];
                        //Итоговые баллы
                        $EventDB['Participants']["p-".$Participant['ID']]['TPoint'] = $Participant['TPoint'];
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
                $EventDB['Participants'][$ParticipantID]['TES']       = (float)$xml_line->Segment_Running->Prf_Details['TES'];
                //Баллы за компоненты
                $EventDB['Participants'][$ParticipantID]['TCS']       = (float)$xml_line->Segment_Running->Prf_Details['TCS'];
                //Баллы Бонус
                $EventDB['Participants'][$ParticipantID]['Bonus']     = (float)$xml_line->Segment_Running->Prf_Details['Bonus'];
                //Баллы Снижение
                $EventDB['Participants'][$ParticipantID]['DedSum']    = (float)$xml_line->Segment_Running->Prf_Details['Ded_Sum'];
                //Элементы
                if(is_object($xml_line->Segment_Running->Prf_Details->Element_List)) {
                    foreach ($xml_line->Segment_Running->Prf_Details->Element_List->Element as $Element) {
                        $ElementID = "e" . $Element['Index'];
                        //Сокращенное наименование элементов
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Name']     = (string)$Element['Elm_Name'];
                        //Баллы
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Points']   = (float)$Element['Points'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['BV']       = (float)$Element['Elm_XBV'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['GOE']      = (float)$Element['Elm_XGOE'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Info']     = (string)$Element['Elm_Info'];
                        //Элемент защитан или нет
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Half']     = (int)$Element['Elm_Half'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['BVWB']     = (float)$Element['Elm_XBVWB'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Review']   = (int)$Element['Elm_Review'];
                        //Полное наименование элементов
                        $EventDB['Participants'][$ParticipantID]['Element'][$ElementID]['Fullname'] = (string)$Element['Elm_Name_Long'];
                        unset($ElementID);
                    }
                    ksort($EventDB['Participants'][$ParticipantID]['Element']);
                }
                //Нарушения (Пока незнаю что за хрень)
                if(is_object($xml_line->Segment_Running->Prf_Details->Deduction_List)) {
                    foreach ($xml_line->Segment_Running->Prf_Details->Deduction_List->Deduction as $Deduction) {
                        $DeductionID = "d" . $Deduction['Index'];
                        //Название нарушения
                        $EventDB['Participants'][$ParticipantID]['Deduction'][$DeductionID]['Name']  = (string)$EventDB['Deduction']['d'.$Deduction['Index']]['Name'];
                        //Баллы
                        $EventDB['Participants'][$ParticipantID]['Deduction'][$DeductionID]['Value'] = (float)$Deduction['Ded_Value'];
                        //
                        $EventDB['Participants'][$ParticipantID]['Deduction'][$DeductionID]['Count'] = (int)$Deduction['Ded_Count'];
                        unset($DeductionID);
                    }
                    ksort($EventDB['Participants'][$ParticipantID]['Deduction']);
                }
                //Критерии (Пока незнаю что за хрень)
                if(is_object($xml_line->Segment_Running->Prf_Details->Criteria_List)) {
                    foreach ($xml_line->Segment_Running->Prf_Details->Criteria_List->Criteria as $Criteria) {
                        $CriteriaID = "c" . $Criteria['Index'];
                        //Сокращенное наименование элементов
                        $EventDB['Participants'][$ParticipantID]['Criteria'][$CriteriaID]['Name']   = (string)$EventDB['Criteria']['c'.$Criteria['Index']]['Name'];
                        //Баллы
                        $EventDB['Participants'][$ParticipantID]['Criteria'][$CriteriaID]['Points'] = (int)$Criteria['Points'];
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
                        $EventDB['Participants'][$ParticipantID]['Sort']      = $Performance['Index'];
                        //Если участник отсутствует
                        if ($Performance['Status'] != "OK") {
                            //Место
                            $EventDB['Participants'][$ParticipantID]['Rank']      = 0;
                            //Балы за прокат
                            $EventDB['Participants'][$ParticipantID]['SeqPoints'] = 0;
                        }
                        else {
                            //Место
                            $EventDB['Participants'][$ParticipantID]['Rank']      = (int)$Performance['Rank'];
                            //Балы за прокат
                            $EventDB['Participants'][$ParticipantID]['SeqPoints'] = (float)$Performance['Points'];
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
                        //Итоговая сортировка
                        $EventDB['Participants'][$ParticipantID]['TSort']  = $Participant['TIndex'];
                        //Если участник отсутствует
                        if ($Participant['Status'] != "ACT") {
                            //Итоговое место
                            $EventDB['Participants'][$ParticipantID]['TRank']  = 0;
                            //Итоговое количество баллов
                            $EventDB['Participants'][$ParticipantID]['TPoint'] = 0;
                        }
                        else {
                            //Итоговое место
                            $EventDB['Participants'][$ParticipantID]['TRank']  = (int)$Participant['TRank'];
                            //Итоговое количество баллов
                            $EventDB['Participants'][$ParticipantID]['TPoint'] = (float)$Participant['TPoint'];
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
                $ParticipantID = "p-" . $xml_line->Segment_Running->Action['Current_Participant_ID'];
                $ReturnJsonToWeb = [
                    "timestamp"  => time(),
                    "dAction"    => "1SC",
                    "EventName"  => $EventDB["Name"],
                    "pCategory"  => $EventDB["Category"]["Name"],
                    "pSegment"   => $EventDB["Segment"]["Name"],
                    "pName"      => $EventDB['Participants'][$ParticipantID]['FullName'],
                    "pClub"      => $EventDB['Participants'][$ParticipantID]['Club'],
                    "pNation"    => $EventDB['Participants'][$ParticipantID]['Nation'],
                    "pTES"       => $EventDB['Participants'][$ParticipantID]['TES'],
                    "pTCS"       => $EventDB['Participants'][$ParticipantID]['TCS'],
                    "pBonus"     => $EventDB['Participants'][$ParticipantID]['Bonus'],
                    "pDedSum"    => $EventDB['Participants'][$ParticipantID]['DedSum'],
                    "pSeqPoints" => $EventDB['Participants'][$ParticipantID]['SeqPoints'],
                    "pTPoint"    => $EventDB['Participants'][$ParticipantID]['TPoint'],
                    "pTRank"     => $EventDB['Participants'][$ParticipantID]['TRank'],
                    "Element"    => $EventDB['Participants'][$ParticipantID]['Element'],
                    "Deduction"  => $EventDB['Participants'][$ParticipantID]['Deduction'],
                ];
                unset($ParticipantID);

                echo "---------------------------------------------------------------------\n";
                echo "Action: " . $ReturnJsonToWeb['dAction'] . ";\n";
                echo "Участник (FullName): " . $ReturnJsonToWeb['pName'] . ";\n";
                echo "Клуб (Club): " .      $ReturnJsonToWeb['pClub'] . ";\n";
                echo "Национальность или регион (Nation): " .    $ReturnJsonToWeb['pNation'] . ";\n";
                echo "(TES): " .       $ReturnJsonToWeb['pTES'] . ";\n";
                echo "TCS: " .       $ReturnJsonToWeb['pTCS'] . ";\n";
                echo "Bonus: " .     $ReturnJsonToWeb['pBonus'] . ";\n";
                echo "DedSum: " .    $ReturnJsonToWeb['pDedSum'] . ";\n";
                echo "SeqPoints: " . $ReturnJsonToWeb['pSeqPoints'] . ";\n";
                echo "TPoint: " .    $ReturnJsonToWeb['pTPoint'] . ";\n";
                echo "TRank: " .      $ReturnJsonToWeb['pTRank'] . ";\n";
            }
            //Показать индивидуальные результаты проката
            elseif ($CommandAction == '2SC') {
                $ParticipantID = "p-" . $xml_line->Segment_Running->Action['Current_Participant_ID'];
                $ReturnJsonToWeb = [
                    "timestamp"  => time(),
                    "dAction"    => "2SC",
                    "EventName"  => (string)$EventDB["Name"],
                    "pCategory"  => (string)$EventDB["Category"]["Name"],
                    "pSegment"   => (string)$EventDB["Segment"]["Name"],
                    "pName"      => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['FullName'],
                    "pClub"      => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['Club'],
                    "pNation"    => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['Nation'],
                    "pTES"       => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['TES'],
                    "pTCS"       => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['TCS'],
                    "pBonus"     => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['Bonus'],
                    "pDedSum"    => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['DedSum'],
                    "pSeqPoints" => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['SeqPoints'],
                    "pTPoint"    => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['TPoint'],
                    "pRank"      => (string)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['Rank'],
                ];
                unset($ParticipantID);

                echo "---------------------------------------------------------------------\n";
                echo "Action: 2SC;\n";
                echo "Участник (FullName): " . $ReturnJsonToWeb['pName'] . ";\n";
                echo "Клуб (Club): " .      $ReturnJsonToWeb['pClub'] . ";\n";
                echo "Национальность или регион (Nation): " .    $ReturnJsonToWeb['pNation'] . ";\n";
                echo "(TES): " .       $ReturnJsonToWeb['pTES'] . ";\n";
                echo "TCS: " .       $ReturnJsonToWeb['pTCS'] . ";\n";
                echo "Bonus: " .     $ReturnJsonToWeb['pBonus'] . ";\n";
                echo "DedSum: " .    $ReturnJsonToWeb['pDedSum'] . ";\n";
                echo "SeqPoints: " . $ReturnJsonToWeb['pSeqPoints'] . ";\n";
                echo "TPoint: " .    $ReturnJsonToWeb['pTPoint'] . ";\n";
                echo "Rank: " .      $ReturnJsonToWeb['pRank'] . ";\n";
            }
            //Информация об участнике
            elseif ($CommandAction == 'NAM') {
                $ReturnJsonToWeb = [
                    "timestamp" => time(),
                    "dAction" => "NAM",
                    "EventName"  => (string)$EventDB["Name"],
                    "pCategory"  => (string)$EventDB["Category"]["Name"],
                    "pSegment"   => (string)$EventDB["Segment"]["Name"],
                    "pName"   => "".$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['FullName'],
                    "pNation" => "".$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['Nation'],
                    "pClub"   => "".$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['Club'],
                    "pMusic"  => "".$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['Music'],
                    "pCoach"  => "".$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['Coach'],
                ];
                echo "---------------------------------------------------------------------\n";
                echo "Action: Name;\n";
                echo "Full: " .    $ReturnJsonToWeb['pName'] . ";\n";
                echo "Club: " .    $ReturnJsonToWeb['pClub'] . ";\n";
                echo "Nation: " .  $ReturnJsonToWeb['pNation'] . ";\n";
                echo "Music: " .   $ReturnJsonToWeb['pMusic'] . ";\n";
                echo "Coach: " .   $ReturnJsonToWeb['pCoach'] . ";\n";
            }
            //Очистка экрана
            elseif ($CommandAction == 'CLR' or $CommandAction == 'STP') {
                echo "Очистка экрана\n";
                $ReturnJsonToWeb = [
                    "timestamp"    => time(),
                    "dAction"      => "Clear",
                ];
            }
            //Информация об судьях
            elseif ($CommandAction == 'JDG') {
                $JudgeID        = $xml_line->Segment_Running->Action['Judge_ID'];
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
                $JudgeID = 0;
            }
            //Стартовый лист
            //Список группы разминки
            //Список промежуточных результатов соревнования
            elseif ($CommandAction == 'STL' || $CommandAction == 'WUP' || $CommandAction == '3SC') {
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
                    $ReturnJsonToWeb["pCurrentGroup"] = (int)$EventDB['Participants']["p-".$xml_line->Segment_Running->Action['Current_Participant_ID']]['GroupNumber'];
                    echo "Action: WUP;\n";
                    echo "CurrentGroupNumber: "  . $ReturnJsonToWeb["pGroup"] . ";\n";
                }
                elseif ($CommandAction == '3SC') {
                    $ReturnJsonToWeb["dAction"] = '3SC';
                    echo "Action: 3SC;\n";
                }

                echo "EventName: " . $ReturnJsonToWeb['EventName'] . ";\n";
                echo "CategoryName: " . $ReturnJsonToWeb['pCategory'] . ";\n";
                echo "SegmentName: " . $ReturnJsonToWeb['pSegment'] . ";\n";

                foreach ($EventDB['Participants'] as $ParticipantStr) {
                    if ($CommandAction == 'STL' || $CommandAction == 'WUP') {
                        $idLine = (int)$ParticipantStr['StartNumber'];
                    }
                    elseif ($CommandAction == '3SC') {
                        $idLine = (int)$ParticipantStr['TSort'];
                    }
                    //Для WUP (Группа разминки)
                    //Пропускаем участника не из своей группы разминки
                    if ($CommandAction == 'WUP' && $ReturnJsonToWeb["pCurrentGroup"] != $ParticipantStr['GroupNumber']) {
                        echo "StartNumber: "  . $ParticipantStr['StartNumber'] . ";\n";
                        echo "GroupNumber: "  . $ParticipantStr['GroupNumber'] . ";\n";
                        continue;
                    }

                    $ReturnJsonToWeb["pParticipant"][$idLine] = [
                        "ID"           => $ParticipantStr["ID"],
                        "pStartNumber" => (int)$ParticipantStr["StartNumber"],
                        "pGroupNumber" => (int)$ParticipantStr["GroupNumber"],
                        "pFullName"    => (string)$ParticipantStr["FullName"],
                        "pNation"      => (string)$ParticipantStr["Nation"],
                        "pTRank"       => (int)$ParticipantStr["TRank"],
                        "pTPoint"      => (string)$ParticipantStr["TPoint"],
                        "pTSort"       => (int)$ParticipantStr["TSort"],
                        "pStatus"      => (string)$ParticipantStr["Status"],
                        "pCurrent"     => 2
                    ];
                    if ($ParticipantStr['ID'] === (int)$xml_line->Segment_Running->Action['Current_Participant_ID']) {
                        $ReturnJsonToWeb["pParticipant"][$idLine]["pCurrent"]  = 1;
                    }

                    echo "-----------------\n";
                    echo "StartLine: "    . $idLine . ";\n";
                    echo "ID: "           . $ReturnJsonToWeb["pParticipant"][$idLine]['ID'] . ";\n";
                    echo "StartNumber: "  . $ReturnJsonToWeb["pParticipant"][$idLine]['pStartNumber'] . ";\n";
                    echo "GroupNumber: "  . $ReturnJsonToWeb["pParticipant"][$idLine]['pGroupNumber'] . ";\n";
                    echo "FullName: "     . $ReturnJsonToWeb["pParticipant"][$idLine]['pFullName'] . ";\n";
                    echo "Nation: "       . $ReturnJsonToWeb["pParticipant"][$idLine]['pNation'] . ";\n";
                    echo "TRank: "        . $ReturnJsonToWeb["pParticipant"][$idLine]['pTRank'] . ";\n";
                    echo "TPoint: "       . $ReturnJsonToWeb["pParticipant"][$idLine]['pTPoint'] . ";\n";
                    echo "TSort: "        . $ReturnJsonToWeb["pParticipant"][$idLine]['pTSort'] . ";\n";
                    echo "Status: "       . $ReturnJsonToWeb["pParticipant"][$idLine]['pStatus'] . ";\n";
                    if ($CommandAction == '3SC') {
                        echo "Current: "  . $ReturnJsonToWeb["pParticipant"][$idLine]["pCurrent"] . ";\n";
                    }
                }
                ksort($ReturnJsonToWeb["pParticipant"],0);
                foreach ($ReturnJsonToWeb["pParticipant"] as $ParticipantStr) {
                    echo $ParticipantStr['pStartNumber'] . "-";
                }
            }
            // Церемония награждения
            elseif ($CommandAction == 'VTR') {
                $SubCommandAction = $xml_line->Segment_Running->Action['Sub_Command'];
                //Приглашение на награждение участников
                if ($SubCommandAction == 5) {
                    $ReturnJsonToWeb = [
                        "timestamp"    => time(),
                        "dAction"      => "VictoryStart",
                        "pCategory"    => (string)$EventDB["Category"]["Name"],
                        "pSegment"     => (string)$EventDB["Segment"]["Name"],
                    ];
                    echo "---------------------------------------------------------------------\n";
                    echo "Action: VictoryStart;\n";
                }
                // Первое место
                elseif ($SubCommandAction == 1) {
                    $ReturnJsonToWeb = [
                        "timestamp" => time(),
                        "dAction"   => "VictoryFirst",
                        "pFullName" => "",
                    ];
                    foreach ($EventDB['Participants'] as $ParticipantStr) {
                        if ($ParticipantStr["TRank"] == 1) {
                            $ReturnJsonToWeb["pFullName"] = $ParticipantStr["FullName"];
                        }
                    }
                    echo "---------------------------------------------------------------------\n";
                    echo "Action: VictoryFirst;\n";
                    echo "FullName: " . $ReturnJsonToWeb["pFullName"] . ";\n";
                }
                // Второе место
                elseif ($SubCommandAction == 2) {
                    $ReturnJsonToWeb = [
                        "timestamp" => time(),
                        "dAction"   => "VictorySecond",
                        "pFullName" => "",
                    ];
                    foreach ($EventDB['Participants'] as $ParticipantStr) {
                        if ($ParticipantStr["TRank"] == 2) {
                            $ReturnJsonToWeb["pFullName"] = $ParticipantStr["FullName"];
                        }
                    }
                    echo "---------------------------------------------------------------------\n";
                    echo "Action: VictorySecond;\n";
                    echo "FullName: " . $ReturnJsonToWeb["pFullName"] . ";\n";
                }
                // Третье место
                elseif ($SubCommandAction == 3) {
                    $ReturnJsonToWeb = [
                        "timestamp" => time(),
                        "dAction"   => "VictoryThird",
                        "pFullName" => "",
                    ];
                    foreach ($EventDB['Participants'] as $ParticipantStr) {
                        if ($ParticipantStr["TRank"] == 3) {
                            $ReturnJsonToWeb["pFullName"] = $ParticipantStr["FullName"];
                        }
                    }
                    echo "---------------------------------------------------------------------\n";
                    echo "Action: VictoryThird;\n";
                    echo "FullName: " . $ReturnJsonToWeb["pFullName"] . ";\n";
                }
                //Подиум (Все места)
                elseif ($SubCommandAction == 0) {
                    $ReturnJsonToWeb = [
                        "timestamp"    => time(),
                        "dAction"      => "VictoryAll",
                        "pParticipant" => [],
                    ];
                    foreach ($EventDB['Participants'] as $ParticipantStr) {
                        if ($ParticipantStr["TRank"] == 1) {
                            $ReturnJsonToWeb["pParticipant"][0] = [
                                "pFullName" => $ParticipantStr["FullName"],
                                "pNation"   => $ParticipantStr["Nation"],
                                "pTRank"    => $ParticipantStr["TRank"],
                            ];
                        }
                    }
                    echo "---------------------------------------------------------------------\n";
                    echo "Action: VictoryAll;\n";
                }
                
            }
            //Segment
            //Сегменты
            elseif ($CommandAction == 'SEG') {
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
            }
            // Таймер старт 
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
            // Таймер отсчет
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
            // Таймер изменен
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
            // Таймер пауза
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
            // Таймер стоп
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
            // Элементы для ТВ, короткое
            elseif ($CommandAction == 'LTV') {
                $ReturnJsonToWeb = [
                    "timestamp" => time(),
                    "dAction"   => "LiveTV",
                    "TES"                => (string)$xml_line->Segment_Running->Prf_Details['TES'],
                    "TES_Leader"         => (string)$xml_line->Segment_Running->Prf_Details['TES_Leader'],
                    "TES_Leader_Overall" => (string)$xml_line->Segment_Running->Prf_Details['TES_Leader_Overall'],
                ];

                echo "---------------------------------------------------------------------\n";
                echo "Action: LiveTV;\n";
                echo "TES: " . $ReturnJsonToWeb['TES'] . ";\n";
                echo "TES_Leader: " . $ReturnJsonToWeb['TES_Leader'] . ";\n";
                echo "TES_Leader_Overall: " . $ReturnJsonToWeb['TES_Leader_Overall'] . ";\n";
            }
            // Элементы для ТВ, подробное
            elseif ($CommandAction == 'ELS') {
                $ReturnJsonToWeb = [
                    "timestamp" => time(),
                    "dAction"   => "LiveTV2",
                    "Points"    => (string)$xml_line->Segment_Running->Prf_Details['Points'],
                    "TES"       => (string)$xml_line->Segment_Running->Prf_Details['TES'],
                    "TCS"       => (string)$xml_line->Segment_Running->Prf_Details['TCS'],
                    "Bonus"     => (string)$xml_line->Segment_Running->Prf_Details['Bonus'],
                    "Ded_Sum"   => (string)$xml_line->Segment_Running->Prf_Details['Ded_Sum'],
                ];

                echo "---------------------------------------------------------------------\n";
                echo "Action: LiveTV;\n";
                echo "TES: " . $ReturnJsonToWeb['TES'] . ";\n";
                echo "TES_Leader: " . $ReturnJsonToWeb['TES_Leader'] . ";\n";
                echo "TES_Leader_Overall: " . $ReturnJsonToWeb['TES_Leader_Overall'] . ";\n";
            }
        }

        if (array_key_exists('dAction', $ReturnJsonToWeb)) {
            foreach($users as $connection) {
                $connection->send(json_encode($ReturnJsonToWeb));
            }
        }
        if ($xml_line->Segment_Running->Action['Command'] != 'TIM') {
            $DBFile = fopen(__DIR__ . '/config/DB.json', 'w');
            fwrite($DBFile, json_encode($EventDB, JSON_PRETTY_PRINT));
            fclose($DBFile);
        }

        empty($ReturnJsonToWeb);
    }
    return 1;
}


$randFileName = rand();
$ErrorLogFile =    fopen(__DIR__ . '/logs/Error-' . date('Y-m-d-H') . '-' . $randFileName . '.log', 'w');
$RawInputLogFile = fopen(__DIR__ . '/logs/RawInput-' . date('Y-m-d') . '-' . $randFileName . '.log', 'w');

require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
//TcpConnection::$defaultMaxSendBufferSize = 2*1024*1024;
// create a ws-server. all your users will connect to it
$ws_worker = new Worker("websocket://0.0.0.0:8000");

// storage of user-connection link
$users = [];

$ws_worker->onConnect = function($connection) use (&$users) {
    $connection->onWebSocketConnect = function($connection) use (&$users) {
        $users[$connection->id] = $connection;
    };
    echo "Клиент WebSocket Подключился\n";
};

$ws_worker->onMessage = function($connection, $data) use (&$users) {
    global $EventDB;
    if ($data == "INIT" && isset($EventDB["Name"])) {
        if (is_object($users[$connection->id])) {
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

            $users[$connection->id]->send(json_encode($ReturnJsonToWeb));
            echo "Отправка\n";
        }
        
    }
};

$ws_worker->onClose = function($connection) use(&$users) {
    // unset parameter when user is disconnected
    unset($users[$connection->id]);
    echo "Клиент WebSocket Отключился\n";
};

// it starts once when you start server.php:
$ws_worker->onWorkerStart = function() use (&$users) {
    global $ini;
    $connection = new AsyncTcpConnection("tcp://" . $ini['CALC_IP'] . ":". $ini['CALC_PORT']);
    $connection->maxSendBufferSize = 4*1024*1024;
    $connection->onConnect = function($connection) {
        echo "Мы подключились к Calc!\n";
    };
    $EventDB = [];
    $stop = 1;
    $NewData = '';
    $connection->onMessage = function($connection, $data) use (&$users) {
        global $NewData;
        global $ErrorLogFile;
        global $RawInputLogFile;        
        fwrite($RawInputLogFile, $data);
        fwrite($ErrorLogFile, "Start LINE---------" . $data . "----------------------Stop Line" . PHP_EOL);
        $stopSimbol = ord(substr($data, -1));
        fwrite($ErrorLogFile, "------Stop simbol:" . $stopSimbol . PHP_EOL);
        if ($stopSimbol == 3 && $NewData == '') {
            $NewData = $data;
            fwrite($ErrorLogFile, "------STRING OK One LINE" . PHP_EOL);
        }
        elseif ($stopSimbol == 3 && $NewData != '') {
            $NewData .= $data;
            fwrite($ErrorLogFile, "------EMPTY STRING3333" . PHP_EOL);
        }
        else {
            $NewData .= $data;
            fwrite($ErrorLogFile, "------EMPTY STRING2222" . PHP_EOL);
        }

        if ($stopSimbol == 3) {
            foreach (explode(chr(3), $NewData) as $data_line1) {
                $startSimbol = ord(substr($data_line1, 0));
                if ($startSimbol == 2) {
                    fwrite($ErrorLogFile, "------#######" . ltrim($data_line1) . PHP_EOL);
                    FuncWorksCalc(ltrim($data_line1), $connection);
                }
                elseif ($startSimbol == 0) {
                    fwrite($ErrorLogFile, "------EMPTY STRING" . PHP_EOL);
                }
                else {
                    fwrite($ErrorLogFile, "------XZ" . PHP_EOL);
                }
            }
            $NewData = '';
            //unset($NewData);
            fwrite($ErrorLogFile, "------EMPTY line" . PHP_EOL);
        }
    };
    $connection->onClose = function($connection) {
        echo "Отключились от Calc\n";
        // Переподключаемся
        $connection->reConnect(5);
    };
    $connection->connect();
};

// Run worker
Worker::runAll();