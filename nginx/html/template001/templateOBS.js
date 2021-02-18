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

// Количество линий участников
LineCountWeb = 10;



/* ################################################################################################
Информационная панель:
    Кнопка: Name; - Информация об участнике
    Переменные:
        1) ${data['EventName']} - Название соревнования
        2) ${data['Category']}  - Название категории соревнования
        3) ${data['Segment']}   - Название сегмента (КП ПП Элементы и т.д.)
        4) ${data['Fullname']}  - ФИО Участника
        5) ${data['Club']}      - Клуб участника
        6) ${data['City']}      - Город участника
        7) ${data['Coach']}     - ФИО тренера или тренеров
        8) ${data['Music']}     - Название музыкально произведения
*/
const FS_UserInfo = (data) => `
<div id="boardPersonal" class="cl_board0">
    <div id="board-circle1"></div>
    <div id="board3"></div>
    <div id="board00">
        <div id="board1"></div>
        <div id="board2"></div>
        <div id="LineUP">${data['Fullname']} (${data['City']})</div>
        <div id="LineDownThree">Тренер: ${data['Coach']}</div>
        <div id="LineDownTwo">Школа:  ${data['Club']}</div>
        <div id="LineDownOne">Музыка: ${data['Music']}</div>
    </div>
</div>`;
/* ################################################################################################
Информационная панель:
    Кнопка: 2nd Score;  - Показать индивидуальные результаты выступления
    Переменные:
        1) ${data['EventName']} - Название соревнования
        2) ${data['Category']}  - Название категории соревнования
        3) ${data['Segment']}   - Название сегмента (КП ПП Элементы и т.д.)
        4) ${data['Fullname']}  - ФИО Участника
        5) ${data['Club']}      - Город участника
        6) ${data['TechnicPoint']}   - Баллы за технику
        7) ${data['ComponentPoint']} - Баллы за компоненты
        8) ${data['DeductionPoint']} - Баллы за нарушения
        9) ${data['Points']}         - Баллы за выступление
       10) ${data['Rank']}           - Место
*/
const FS_UserResult = (data) => `
<div id="boardPersonal" class="cl_board0">
    <div id="board-circle1"></div>
    <div id="board3"></div>
    <div id="board00">
        <div id="board1"></div>
        <div id="board2"></div>
        <div id="LineUP">${data['Fullname']} / ${data['Club']}<span id="pRank">${data['Rank']} </span></div>
        <div id="LineDown">Техника: ${data['TechnicPoint']};  Компоненты: ${data['ComponentPoint']};  Снижения: ${data['DeductionPoint']};  Баллы за выступление: ${data['Points']}</div>
    </div>
</div>`;
/* ################################################################################################
Информационная панель:
    Кнопка: Judge -> Send; - Информация об официальном лице (судья)
    Переменные:
        1) ${data['EventName']} - Название соревнования
        2) ${data['Category']}  - Название категории соревнования
        3) ${data['Segment']}   - Название сегмента (КП ПП Элементы и т.д.)
        4) ${data['Fullname']}  - ФИО официального представителя
        5) ${data['Club']}      - Город официального представителя
        6) ${data['Proff']}     - Должность (роль) официального представителя
*/
const FS_JudgeOne = (data) => `
<div id="boardPersonal" class="cl_board0">
    <div id="board-circle1"></div>
    <div id="board3"></div>
    <div id="board00">
        <div id="board1"></div>
        <div id="board2"></div>
        <div id="LineUP">${data['Fullname']} (${data['Nation']}) <small>${data['Club']}</small></div>
        <div id="LineDown">${data['Proff']}</div>
    </div>
</div>`;
/* ################################################################################################
Информационная панель:
    Кнопка: Start List; - Стартовый лист (Полный)
    Кнопка: WarmG;      - Стартовый лист (По группам разминки)
    Кнопка: Judge -> Send All Judges; - Информация обо всех судьях
    Переменные:
        1) ${data['EventName']} - Название соревнования
        2) ${data['Category']}  - Название категории соревнования
        3) ${data['Segment']}   - Название сегмента (КП ПП Элементы и т.д.)
        4) ${data['SubName']}   - Дополнительные заголовки
        5) ${data['PlaceLine']} - Список участников
*/
const FS_UsersList = (data) => `
<div id="boardGroup" class="cl_board20">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'>${data['EventName']}</div>
            <div id='CategoryName'>${data['Category']}</div>
            <hr />
            <div id='SegmentName'>${data['Segment']}</div> / <div id='SubName'>${data['SubName']}</div>
        </div>
    </div>
    <div id="participantListContainer">${data['PlaceLine']}</div>
</div>`;
/* Шаблон: Первого блока обёртки */
const FS_NameLineWrapperFirst = (data) => `<div data-index="${data['IDContainer']}" class="participantListContainerItem container-fluid active">`;
/* Шаблон: Последующих блоков обёрток */
const FS_NameLineWrapperSecond = (data) => `</div><div data-index="${data['IDContainer']}" class="participantListContainerItem container-fluid">`;
/* Шаблон: Списка участников */
const FS_NameLineParticipant = (data) => `<div class='row LineRow'><div class="col LineText">${data["Sort"]}) ${data["FullName"]} <small>  ${data['Status']}</small><span class="Nation">${data["Nation"]}</span></div></div>`;
/* Шаблон: Списка участников с оценками */
const FS_3SCLineParticipant = (data) => `<div class='row LineRow ${data["CurrentClass"]}'><div class="col LineText">${data["Sort"]}) ${data["FullName"]}<br><span class="Nation">${data["Nation"]}</span></div><div class='col-3 LineLast'>${data["Point"]}</div></div>`;
/* Шаблон: Пустая строка между участниками */
const FS_LineTextEmpty = `<div class='row LineRow'><div class="col LineTextEmpty">.....</div></div>`;
/* Шаблон: Список официальных лиц */
const FS_JugeAllLine = (data) => `<div class='row LineRow'><div class="col LineText">${data["Proff"]} / ${data["FullName"]} <span class="Nation">${data["Nation"]}</span></div></div>`;
/* ################################################################################################
Информационная панель:
    Кнопка: 3nd Score; - Промежуточные результаты соревнования
    Переменные:
        1) ${data['EventName']} - Название соревнования
        2) ${data['Category']}  - Название категории соревнования
        3) ${data['Segment']}   - Название сегмента (КП ПП Элементы и т.д.)
        4) ${data['SubName']}   - Дополнительные заголовки
        5) ${data['PlaceLine']} - Список участников
*/
const FS_ListResult = (data) => `
<div id="boardGroup" class="cl_boardGroupResult">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='SubName'>${data['SubName']}</div>
        </div>
    </div>
    <div id="participantListContainer">${data['PlaceLine']}</div>
</div>`;
/* ################################################################################################
Информационная панель:
    Кнопка: Segment; - Название соревнования, группа и сегмент
    Переменные:
        1) ${data['EventName']} - Название соревнования
        2) ${data['Category']}  - Название категории соревнования
        3) ${data['Segment']}   - Название сегмента (КП ПП Элементы и т.д.)
*/
const FS_EventName = (data) => `
<div id="boardSegment" class="cl_boardEvent">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'>${data['EventName']}</div>
            <hr style="width:60%;">
            <div id='CategoryName'>${data['Category']}</div>
            <hr style="width:60%;">
            <div id='SegmentName'>${data['Segment']}</div>
        </div>
    </div>
</div>`;

/* ################################################################################################
Информационная панель:
    Кнопка: V.Cerem -> Send Victory Ceremony; - Приглашение на церемонию награждения
    Переменные:
        1) ${data['EventName']} - Название соревнования
*/
const FS_VictoryStart = (data) => `
<div id="boardSegment" class="cl_boardEvent">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'>${data['EventName']}</div>
            <hr style="width:60%;">
            <div>Церемония награждения</div>
        </div>
    </div>
</div>`;

/* ################################################################################################
Информационная панель:
    Кнопка: V.Cerem -> Send Podium; - Показать все призовые места
    Переменные:
        1) ${data['EventName']} - Название соревнования
        2) ${data['PlaceLine']} - Список участников
        3) ${data['Fullname']}  - ФИО Участника
        4) ${data['City']}      - Город участника
        5) ${data['VictoryPlaсe']} - Итоговое место
*/
const FS_VictoryAll = (data) => `
<div id="boardGroup" class="cl_boardGroupResult">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'>${data['EventName']}</div>
        </div>
    </div>
    <div id="participantListContainer"><div id="participantListContainerOne" class="container-fluid participantListContainerIn">${data['PlaceLine']}</div></div>
</div>`;
const FS_VictoryAllLine = (data) => `<div class='row ListRow'><div class="col LineText">${data["VictoryPlaсe"]}) ${data["FullName"]} <span class="Nation">${data["City"]}</span></div></div>`;
/* ################################################################################################
Информационная панель:
    Кнопка: V.Cerem -> Send Gold, Silver, Bronze; - Показать призовое место: Золото, Серебро или Бронза
    Переменные:
        1) ${data['EventName']} - Название соревнования
        2) ${data['Fullname']}  - ФИО Участника
        3) ${data['Club']}      - Город участника
        4) ${data['VictoryPlaсe']} - Итоговое место
        5) ${data['Music']}     - Название музыкально произведения

*/
const FS_VictoryPlace = (data) => `
<div id="boardPersonal" class="cl_board0">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'>${data['EventName']}</div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div id="LineName" class="col">${data['Fullname']}</div>
        </div>
        <div id="LineNation" class="row">
            <div class="col">${data['Club']}</div>
        </div>
        <div id="LineProff" class="row">
            <div class="col">${data['VictoryPlaсe']}</div>
        </div>
    </div>
</div>`;
