
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

var Participant = [];
var EventDB = [];
var JsonData;
var boardOpen = false;
var boardPersonalOpen = false;
var boardSegmentOpen = false;
var boardGroupOpen = false;
var boardKissAndCryOpen = false;
var timerBoardOpen = false;
var boardConfigure = false;


// Таймер закрытия панели
let timerCloseBoardPersonal;
// Таймер закрытия панели
let timerCloseBoardSegment;
// Таймер закрытия панели
let timerCloseBoardGroup;
// Таймер закрытия панели
let timerCloseBoardKissAndCry;

function connect() {
    var ws = new WebSocket('ws://' + window.location.hostname + ':8000');
    ws.onopen = function() {
        if (debuging != false) {console.log('WebSocket connected');};
        if (boardConfigure == false) {
            ws.send("INIT");
            boardConfigure = true;
            EventDB = [];
            if (debuging != false) {console.log('WebSocket send');};
        };
    };

    ws.onmessage = function(evt) {
        JsonData = JSON.parse(evt.data);
        if (JsonData) {
            //Обновить время
            if ((JsonData.dAction).search(/^Timer/) != -1) {
                if (ConfigShowTimer == false) {

                }
                else if (JsonData.dAction == 'Timer') {
                    // Показать табло со временем
                    if (!timerBoardOpen && JsonData.TimerState == 1) {
                        ShowTimerBoard();
                    }
                    updateTimerBoard();
                }
                // Очистить табло со временем
                else if (JsonData.dAction == 'TimerClear') {
                    clearTimerBoard();
                }
                // Показать табло со временем
                else if ((JsonData.dAction == 'TimerCountdown' || JsonData.dAction == 'TimerStart') && !timerBoardOpen) {
                    ShowTimerBoard();
                }
            }
            // Первые данные
            else if (JsonData.dAction == 'INIT') {
                EventDB['EventName']    = JsonData.EventName;
                EventDB['CategoryName'] = JsonData.pCategory;
                EventDB['SegmentName']  = JsonData.pSegment;
                EventDB['TimeAction']   = JsonData.TimeAction;
                if (ConfigKissAndCry) {
                    $("#root_boardSegment").html(FS_EventName);
                    $("#EventName"   ).html(EventDB['EventName']);
                    $("#CategoryName").html(EventDB['CategoryName']);
                    $("#SegmentName" ).html(EventDB['SegmentName']);
                    $("#boardSegment").addClass("cl_boardIn");
                    boardSegmentOpen = true;
                }
            }
            //Очистить экран
            else if (JsonData.dAction == 'CLR' && !ConfigKissAndCry && JsonData.dAction != '1SC') {
                cleanBoard();
            }
            // Очистить экран если панель открыта
            /*else if (boardOpen && !ConfigKissAndCry && JsonData.dAction != '1SC') {
                cleanBoard(1);
            }*/
            else {
                updateBoard();
            }
            if (debuging != false) {console.log('Необходимо обновить данные ');};
        }
        else {
            if (debuging != false) {console.log('WebSocket empty messages');};
        }
    };

    ws.onclose = function(e) {
        EventDB = [];
        if (debuging != false) {console.log('Socket is closed. Reconnect will be attempted in 1 second.', e.reason);};
        setTimeout(function() {
            connect();
        }, 1000);
    };

    ws.onerror = function(err) {
        if (debuging != false) {console.error('Socket encountered error: ', err.message, 'Closing socket');};
        ws.close();
    };
}

function cleanBoardPersonal() {
    if (debuging != false) {console.log('Action: Clear board personal');};
    $( "#boardPersonal" ).removeClass("cl_boardIn");
    $( "#boardPersonal" ).addClass("cl_boardOut");
    const node1 = document.querySelector( '#boardPersonal' );
    if (node1) {
        if (debuging != false) {console.log('Node 1 exist');};
    }
    function handleAnimationEnd1() {
        node1.remove();
        clearTimeout(timerCloseBoardPersonal);
        boardPersonalOpen = false;
    }
    node1.addEventListener('animationend', handleAnimationEnd1(), {once: true});
}
function cleanBoardGroup() {
    if (debuging != false) {console.log('Action: Clear board Group');};
    $( "#boardGroup" ).removeClass("cl_boardIn");
    $( "#boardGroup" ).addClass("cl_boardOut");
    const node2 = document.querySelector( '#boardGroup' );
    function handleAnimationEnd2() {
        node2.remove();
        clearTimeout(timerCloseBoardGroup);
        boardGroupOpen = false;
    }
    node2.addEventListener('animationend', handleAnimationEnd2(), {once: true});
}
function cleanBoardSegment() {
    if (debuging != false) {console.log('Action: Clear board Segment');};
    $( "#boardSegment" ).removeClass("cl_boardIn");
    $( "#boardSegment" ).addClass("cl_boardOut");
    const node3 = document.querySelector( '#boardSegment' );
    function handleAnimationEnd3() {
        node3.remove();
        clearTimeout(timerCloseBoardSegment);
        boardSegmentOpen = false;
    }
    node3.addEventListener('animationend', handleAnimationEnd3(), {once: true});
}
function cleanBoardKissAndCry() {
    if (debuging != false) {console.log('Action: Clear board Kiss And Cry');};
    $("#boardKissAndCry").removeClass("cl_boardIn");
    $("#boardKissAndCry").addClass("cl_boardOut");
    const node4 = document.querySelector('#boardKissAndCry');
    function handleAnimationEnd() {
        node4.remove();
        clearTimeout(timerCloseBoardKissAndCry);
        boardKissAndCryOpen = false;
    }
    node4.addEventListener('animationend', handleAnimationEnd(), {once: true});
}
function cleanBoard(updateBoardNow) {
    if (debuging != false) {console.log('Action: Clear board ');};
    $( "#board" ).removeClass("cl_boardIn");
    $( "#board" ).addClass("cl_boardOut");
    const node = document.querySelector( '#board' );
    function handleAnimationEnd() {
        node.remove();
        //alert('close');
        clearTimeout(timerCloseBoardPersonal);
        clearTimeout(timerCloseBoardSegment);
        clearTimeout(timerCloseBoardGroup);
        boardOpen = false;
        if (updateBoardNow == 1) {
            updateBoard();
        }
    }
    node.addEventListener('animationend', handleAnimationEnd, {once: true});
}

function clearTimerBoard() {
    $( "#id_boardTimer" ).html( "" );
    var promise = document.getElementById('player').pause();
    timerBoardOpen = false;
}

function ShowTimerBoard() {
    $( "#id_boardTimer" ).html( FS_Timer );
    $( ".round-button-time" ).html( JsonData.Time );
    timerBoardOpen = true;
}

function updateTimerBoard() {
    $( ".round-button-time" ).html( JsonData.Time );
    if (JsonData.Time == 1.00 && JsonData.sAction == 'TimerCountdown') {
        var promise = document.getElementById('player').play();
    }
}
		
			
function updateBoard() {
    if (ConfigShowTimer && JsonData.dAction != '1SC') {
        if (boardPersonalOpen) {
            cleanBoardPersonal();
        }
        if (boardGroupOpen) {
            cleanBoardGroup();
        }
        if (boardSegmentOpen) {
            cleanBoardSegment();
        }
    }
    // Табло для KissAndCry
    if (ConfigKissAndCry) {
        if (JsonData.dAction == '1SC') {
            if (boardSegmentOpen) {
                cleanBoardSegment();
            }
            if (boardKissAndCryOpen) {
                cleanBoardKissAndCry();
            }
            if (debuging != false) {console.log('Action 1SC');};
            $("#root_boardKissAndCry").html(FS_KissAndCry);
            $("#EventName"    ).html(JsonData.EventName );
            $("#CategoryName" ).html(JsonData.pCategory );
            $("#SegmentName"  ).html(JsonData.pSegment  );
            $("#pNation"      ).html(JsonData.pNation);
            $("#pClub"        ).html(JsonData.pClub);
            $("#pName"        ).html(JsonData.pName);
            $("#pTES"         ).html(JsonData.pTES);//
            $("#pTCS"         ).html(JsonData.pTCS);//
            $("#pDedSum"      ).html(JsonData.pDedSum == 0.00 ? '-' : `-${JsonData.pDedSum}`);//
            $("#pBonus"       ).html(JsonData.pBonus == 0.00 ? '-' : JsonData.pBonus);
            $("#pSeqPoints"   ).html(JsonData.pSeqPoints);//
            $("#pTPoint"      ).html(JsonData.pTPoint);//
            $("#pTRank"       ).html(JsonData.pTRank);//


            Object.keys(JsonData.Element).forEach( function(itemKey){
                item = JsonData.Element[itemKey];
                    $( "#LineSecond" ).append( `<div><div class="LineTechDetail">${item['Name']}</div><div class="LineTechDetail">${item['Info']}</div><div class="LineTechDetail">${item['BV']}</div><div class="LineTechDetail">${item['GOE']}</div><div class="LineTechDetail">${item['Points']}</div></div>` );
            });
            Object.keys(JsonData.Deduction).forEach( function(itemKey){
                item = JsonData.Deduction[itemKey];
                    $( "#LineFourth" ).append( `<div><div class="LineTechDetail">${item['Name']}</div><div class="LineTechDetail">${item['Value']}</div></div>` );
            });
            $( "#boardKissAndCry" ).addClass("cl_boardIn");
            boardKissAndCryOpen = true;
            timerCloseBoardKissAndCry = setTimeout(function() {
                cleanBoardKissAndCry();
                $("#root_boardSegment").html(FS_EventName);
                $("#EventName"   ).html(EventDB['EventName']);
                $("#CategoryName").html(EventDB['CategoryName']);
                $("#SegmentName" ).html(EventDB['SegmentName']);
                $("#boardSegment").addClass("cl_boardIn");
                boardSegmentOpen = true;
            }, 120000);

        }
    }
    // STL - Стартовый лист
    // WUP - Стартовый лист по группам
    // 3SC - Показать промежуточные результаты соревнования
    // JudgeAll - Информация о всех судьях
    else if (JsonData.dAction == 'STL' || JsonData.dAction == 'WUP' || JsonData.dAction == '3SC' || JsonData.dAction == 'JudgeAll') {
        if (boardGroupOpen && !ConfigShowTimer) {
            cleanBoardGroup();
        }
        if (JsonData.dAction == 'STL') {
            $( "#root_boardGroup").html( FS_UsersList );
            $( "#SubName"        ).html( TitleSubNameStartList );
        }
        else if (JsonData.dAction == 'WUP') {
            $( "#root_boardGroup").html( FS_UsersList );
            $( "#SubName"        ).html( TitleSubNameWup + " " + JsonData.pCurrentGroup);
        }
        else if (JsonData.dAction == '3SC') {
            $( "#root_boardGroup").html( FS_ListResult );
            $( "#SubName"        ).html( TitleSubName3nd );
        }
        else if (JsonData.dAction == 'JudgeAll') {
            $( "#root_boardGroup").html( FS_UsersList );
            $( "#SubName"        ).html( TitleSubNameJudgeAll );
        }
        $( "#EventName"    ).html( JsonData.EventName );
        $( "#CategoryName" ).html( JsonData.pCategory );
        $( "#SegmentName"  ).html( JsonData.pSegment  );

        Participant = JsonData.pParticipant;
        var ListParticipantNumberAll = 1;
        Object.keys(Participant).forEach( function(itemKey, index){
            if (index != 0 && index % LineCountWeb === 0) {
                ListParticipantNumberAll += 1;
            }
        });

        var ListParticipantNumber = 1;
        Object.keys(Participant).forEach( function(itemKey, index){
            if (index == 0) {
                $( "#participantListContainer" ).append( `<div id='participantListContainer${JsonData.dAction != '3SC' ? ListParticipantNumber : 'One'}' class="${(ListParticipantNumberAll > ListParticipantNumber) && JsonData.dAction != '3SC' ? 'participantListContainerInOut' : 'participantListContainerIn'}">` );
            }
            else if (index % LineCountWeb === 0 && JsonData.dAction != '3SC') {
                ListParticipantNumber += 1;
                $( "#participantListContainer" ).append( `</div><div id='participantListContainer${JsonData.dAction != '3SC' ? ListParticipantNumber : 'One'}' class="${(ListParticipantNumberAll > ListParticipantNumber) && JsonData.dAction != '3SC' ? 'participantListContainerInOut' : 'participantListContainerIn'}">` );
            }
            item = Participant[itemKey];
            //Стартовый лист
            if (JsonData.dAction == 'STL' || JsonData.dAction == 'WUP') {
                var ParticipantStatus = '';
                if (item["pStatus"] == "WDR" && JsonData.dAction == 'WUP') {
                    //В начале планировал не выводить отсутствующих участников на разминке
                    //но после попросили выводить всех и даже тех кто отсутствует
                    //return;
                    ParticipantStatus = '';
                }
                if (item["pStatus"] == "WDR") {
                    ParticipantStatus = '(Отсутствует)';
                }
                else if  (item["pStatus"] == "WDR") {
                    ParticipantStatus = '';
                }

                $( `#participantListContainer${ListParticipantNumber}` ).append( `<div class='participantList'><div>${item["pStartNumber"]})  ${item["pFullName"]} <small>  ${ParticipantStatus}</small><span class="Nation">${item["pNation"]}</span></div></div>` );
            }
            //Показать промежуточные результаты соревнования
            else if (JsonData.dAction == '3SC') {
                if (index <= 2 || item["pCurrent"] == 1) {
                    $( `#participantListContainerOne` ).append( `<div class='participantList ${(item["pCurrent"] == 1) ? "participantCurrent" : ""}'><div>${item["pTSort"]}) ${item["pFullName"]}<span class='participantPoint'>${item["pTPoint"]}</span></div></div>` );
                }
                else if ((index == 3 && Object.keys(Participant).length >= 5) || (index == 5 && Object.keys(Participant).length >= 7)) {
                    $( `#participantListContainerOne` ).append( `<div class='participantList'><div class="center">.....</div></div>` );
                }
            }
            //Информация об судьях
            else if (JsonData.dAction == 'JudgeAll') {
                if (debuging != false) {console.log('Action JudgeAll');};

                if (item['dFunction'] == "JDG") {
                    item["pProff"] = `${OfficialFunction['JDG']} ${item['pIndex']}`;
                }
                else {
                    item["pProff"] = OfficialFunction[item['dFunction']];
                }
                $( `#participantListContainer${ListParticipantNumber}` ).append( `<div class='participantList'><div>${index + 1}) ${item["pProff"]} / ${item["pNation"]} / ${item["pFullName"]}</div></div>` );
            }
            
        });
        $( "#participantListContainer" ).append("</div>");
        ListParticipantNumber = 1;
        $( "#boardGroup" ).addClass("cl_boardIn");
        boardGroupOpen = true;
        if (!ConfigShowTimer) {
            timerCloseBoardGroup = setTimeout(function() {
                cleanBoardGroup();
            }, 30000);
        }
    }
    // NAM - Информация об участнике
    // 2SC - Показать индивидуальные результаты проката
    // JudgeOne - Информация об официальном лице
    else if (JsonData.dAction == 'NAM' || JsonData.dAction == '2SC' || JsonData.dAction == 'JudgeOne') {
        if (boardPersonalOpen && !ConfigShowTimer) {
            if (debuging != false) {console.log('Test 3'  . boardPersonalOpen);};
            cleanBoardPersonal();
        }
        //Информация об участнике
        if (JsonData.dAction == 'NAM') {
            if (debuging != false) {console.log('Action NAM');};
            $("#root_boardPersonal").html( FS_UserInfo );
            $("#EventName"    ).html(JsonData.EventName);
            $("#CategoryName" ).html(JsonData.pCategory);
            $("#SegmentName"  ).html(JsonData.pSegment);
            $("#pNation"      ).html(JsonData.pNation);
            $("#pName"        ).html(JsonData.pName);
            $("#pCoach"       ).html(JsonData.pCoach);
            $("#pClub"        ).html(JsonData.pClub);
            $("#pMusic"       ).html(JsonData.pMusic);
        }
        //Информация об официальном лице (Судьи)
        else if (JsonData.dAction == 'JudgeOne') {
            if (debuging != false) {console.log('Action JudgeOne');};
            $("#root_boardPersonal").html( FS_JudgeOne );
            $("#EventName"    ).html(JsonData.EventName);
            $("#CategoryName" ).html(JsonData.pCategory);
            $("#SegmentName"  ).html(JsonData.pSegment);
            $("#pNation"      ).html(JsonData.pNation);
            $("#pClub"        ).html(JsonData.pClub);
            $("#pName"        ).html(JsonData.pName);
            Object.keys(JsonData.pIndex).forEach( function(itemKey){
                if (JsonData.pIndex[itemKey] == "JDG") {
                    $("#pProff").append( `${OfficialFunction['JDG']} ${itemKey}; ` );
                }
                else {
                    $("#pProff").append( OfficialFunction[JsonData.pIndex[itemKey]] + '; ' );
                }
            });
        }
        //Показать индивидуальные результаты проката
        else if (JsonData.dAction == '2SC') {
            if (debuging != false) {console.log('Action 2SC');};
            $("#root_boardPersonal").html(FS_UserResult);
            $("#EventName"   ).html(JsonData.EventName);
            $("#CategoryName").html(JsonData.pCategory);
            $("#SegmentName" ).html(JsonData.pSegment);
            $("#pNation"     ).html(JsonData.pNation);
            $("#pClub"       ).html(JsonData.pClub);
            $("#pName"       ).html(JsonData.pName);
            $("#pTES"        ).html(JsonData.pTES);//
            $("#pTCS"        ).html(JsonData.pTCS);//
            $("#pDedSum"     ).html(JsonData.pDedSum);//
            $("#pBonus"      ).html(JsonData.pBonus);
            $("#pSeqPoints"  ).html(JsonData.pSeqPoints);//
            $("#pRank"       ).html(JsonData.pRank);//
        }
        $( "#boardPersonal" ).addClass("cl_boardIn");
        boardPersonalOpen = true;
        if (!ConfigShowTimer) {
            timerCloseBoardPersonal = setTimeout(function() {
                cleanBoardPersonal();
            }, 40000);
        }
    }
    //Показать название программы выступления
    else if (JsonData.dAction == 'SEG') {
        if (boardSegmentOpen && !ConfigShowTimer) {
            cleanBoardSegment();
        }
        $( "#root_boardSegment").html(FS_EventName );
        $( "#EventName"      ).html(JsonData.EventName);
        $( "#CategoryName"   ).html(JsonData.pCategory);
        $( "#SegmentName"    ).html(JsonData.pSegment);
        $( "#boardSegment"   ).addClass("cl_boardIn");
        boardSegmentOpen = true;
        if (!ConfigShowTimer) {
            timerCloseBoardSegment = setTimeout(function() {
                cleanBoardSegment();
            }, 30000);
        }
    }

    if (debuging != false) {console.log('Play') };
}
connect();
