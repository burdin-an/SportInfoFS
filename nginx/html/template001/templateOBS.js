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

 //Шаблоны для титров

//Информация об участнике
//Показать индивидуальные результаты проката
var FS_UserInfo = `
<div id="boardPersonal" class="cl_board0">
    <div id="board-circle1"></div>
    <div id="board3"></div>
    <div id="board00">
        <div id="board1"></div>
        <div id="board2"></div>
        <div id="LineUP"><span id="pName"></span> / <span id="pNation"></span></div>
        <div id="LineDownThree">Тренер: <span id='pCoach'></span></div>
        <div id="LineDownTwo">Школа: <span id='pClub'></span></div>
        <div id="LineDownOne">Музыка: <span id='pMusic'></span></div>
    </div>
</div>`;

//Показать индивидуальные результаты проката
var FS_UserResult = `
<div id="boardPersonal" class="cl_board0">
    <div id="board-circle1"></div>
    <div id="board3"></div>
    <div id="board00">
        <div id="board1"></div>
        <div id="board2"></div>
        <div id="LineUP"><span id="pName"></span>/ <span id="pNation"></span><span id="pRank"></span></div>
        <div id="LineDown">Техника: <span id='pTES'></span>; Компоненты: <span id='pTCS'></span>; Снижения: <span id='pDedSum'></span>; Баллы за выступление: <span id='pSeqPoints'></span></div>
    </div>
</div>`;

//Информация об официальном лице
var FS_JudgeOne = `
<div id="boardPersonal" class="cl_board0">
    <div id="board-circle1"></div>
    <div id="board3"></div>
    <div id="board00">
        <div id="board1"></div>
        <div id="board2"></div>
        <div id="LineUP"><span id="pName"></span><small>(<span id="pClub"></span>)</small></div>
        <div id="LineDown"><span id='pProff'></span></div>
    </div>
</div>`;

//Стартовый лист - Полный
//Стартовый лист - По группам
//Информация об судьях
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
            <div id='SubName'></div>
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
var FS_Timer = ``;

// Количество линий участников
var LineCountWeb = 10;
// Показать время 
var ConfigShowTimer = false;
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