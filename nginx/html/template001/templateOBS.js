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
 * @version   1.0.2
 */

//Шаблоны для титров

/* ################################################################################################
Информационная панель:
    Кнопка: Name; - Информация об участнике
*/
FS_UserInfo = `
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
/* ################################################################################################
Информационная панель:
    Кнопка: 2nd Score;  - Показать индивидуальные результаты выступления
*/
FS_UserResult = `
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
/* ################################################################################################
Информационная панель:
    Кнопка: Judge -> Send; - Информация об официальном лице (судья)
*/
FS_JudgeOne = `
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
/* ################################################################################################
Информационная панель:
    Кнопка: Start List; - Стартовый лист (Полный)
    Кнопка: WarmG;      - Стартовый лист (По группам разминки)
    Кнопка: Judge -> Send All Judges; - Информация обо всех судьях
*/
FS_UsersList = `
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
/* ################################################################################################
Информационная панель:
    Кнопка: 3nd Score; - Промежуточные результаты соревнования
*/
FS_ListResult = `
<div id="boardGroup" class="cl_boardGroupResult">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='SubName'></div>
        </div>
    </div>
    <div id="participantListContainer"></div>
</div>`;
/* ################################################################################################
Информационная панель:
    Кнопка: Segment; - Название соревнования, группа и сегмент
*/
FS_EventName = `
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

// Количество линий участников
LineCountWeb = 10;
