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

 //Шаблоны для кубика (экран на льду)

//Информация об участнике
var FS_UserInfo = `
<div id="boardPersonal" class="cl_board0">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'></div>
            <div id='CategoryName'></div>
            <hr />
            <div id='SegmentName'></div>
        </div>
    </div>
    <div id="LineName" class="Line"><div class="LineType01" id="pName"></div></div>
    <div id="LineOne" class="Line"><div class="LineType01" id="pClub"></div></div>
    <div id="LineTwo" class="Line"><div class="LineFirst">Тренер:</div><div class="LineType02" id='pCoach'></div></div>
    <div id="LineThree" class="Line"><div class="LineFirst">Музыка:</div><div class="LineType02" id='pMusic'></div></div>
</div>`;

//Показать индивидуальные результаты проката
var FS_UserResult = `
<div id="boardPersonal" class="cl_board0">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'></div>
            <div id='CategoryName'></div>
            <hr />
            <div id='SegmentName'></div>
        </div>
    </div>
    <div id="LineName" class="Line"><div class="LineType01" id="pName"></div></div>
    <div id="LineOne" class="Line"><div class="LineType01" id="pClub"></div></div>
    <div id="LineTwo" class="Line"><div class="LineType03">Техника:</div><div class="LineLast" id='pTES'></div></div>
    <div id="LineThree" class="Line"><div class="LineType03">Компоненты:</div><div class="LineLast" id='pTCS'></div></div>
    <div id="LineFour" class="Line"><div class="LineType03">Снижения:</div><div class="LineLast" id='pDedSum'></div></div>
    <div id="LineFour" class="Line"><div class="LineType03">Баллы за выступление:</div><div class="LineLast" id='pSeqPoints'></div></div>
    <div id="LineFour" class="Line"><div class="LineType03">Место:</div><div class="LineLast" id='pRank'></div></div>
</div>`;

//Информация об официальном лице
var FS_JudgeOne = `
<div id="boardPersonal" class="cl_board0">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'></div>
            <div id='CategoryName'></div>
            <hr />
            <div id='SegmentName'></div>
        </div>
    </div>
    <div id="LineName" class="Line"><div class="LineType01" id="pName"></div></div>
    <div id="LineOne" class="Line"><div class="LineType01 center" id="pClub"></div></div>
    <div id="LineTwo" class="Line"><div class="LineType01" id='pProff'></div></div>
</div>`;

//Стартовый лист - Полный
//Стартовый лист - По группам
//Промежуточные результаты соревнования
//Информация о всех судьях
var FS_UsersList = `
<div id="boardGroup" class="cl_board20">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'></div>
            <div id='CategoryName'></div>
            <hr />
            <div id='SegmentName'></div> / <div id='SubName'></div>
        </div>
    </div>
    <div id="participantListContainer"></div>
</div>`;

//Промежуточные результаты соревнования
var FS_ListResult = `
<div id="boardGroup" class="cl_boardGroupResult">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'></div>
            <div id='CategoryName'></div>
            <hr />
            <div id='SegmentName'></div> / <div id='SubName'></div>
        </div>
    </div>
    <div id="participantListContainer"></div>
</div>`;

//Название программы выступления
var FS_EventName = `
<div id="boardSegment" class="cl_boardEvent">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'></div>
            <hr style="width:60%;">
            <div id='CategoryName'></div>
            <hr style="width:60%;">
            <div id='SegmentName'></div>
        </div>
    </div>
</div>`;

//Таймер
var FS_Timer = `
<div class="cl_boardTimer">
    <div class="round-button">
        <div class="round-button-circle">
            <div class="round-button-time"></div>
        </div>
    </div>
</div>`;

// Количество линий участников
var LineCountWeb = 6;
// Показать время 
var ConfigShowTimer = true;
// Табло "Уголок слёз и поцелуев"
var ConfigKissAndCry = false;
// Отладочная информация
var debuging = true;
// 
var TitleSubNameJudgeAll = "Официальные лица:";
// 
var TitleSubName3nd = "Промежуточные результаты:";
// 
var TitleSubNameWup = "Разминка, группа №:";
// 
var TitleSubNameStartList = "Стартовый лист:";

//Официальные лица
var OfficialFunction = [];
//Судья №1...99
OfficialFunction['JDG'] = "Судья №";
//Помощник технического специалиста
OfficialFunction['STS'] = "Ассистент технического специалиста";
//Технический специалист
OfficialFunction['TSP'] = "Технический специалист";
//Технический контролёр
OfficialFunction['TCO'] = "Технический контролёр";
// Старший судья
OfficialFunction['ERF'] = "Старший судья";
//Оператор ввода данных или видео оператор
OfficialFunction['DOP'] = "Оператор ввода данных";
//Пока незнаю кто это
OfficialFunction['REP'] = "Representative";
//Пока незнаю кто это
OfficialFunction['TDG'] = "Технический делегат";