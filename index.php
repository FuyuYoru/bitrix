<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Контроллинг УПД");

?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Контроллинг УПД</title>
        <link rel="stylesheet" href="./styles/styleV2.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <body>
    <h1>Контроллинг статусов документов</h1>

    <!-- Форма с полем ввода и кнопкой -->
    <form method="post" action="./process.php">
        <label for="numberInput">Выделите поле и сканируйте штрих-код документа:</label>
        <input type="text" name="textInput" id="numberInput" placeholder="Ваш код здесь" autofocus>

        <div class="selectText">
            <!-- Элемент для выбора текста -->
            <select name="selectText" id="selectText">
                <option value="1.На выдаче">1.На выдаче</option>
                <option value="2.Предпроверка">2.Предпроверка</option>
                <option value="3.Сдан">3.Сдан</option>
                <option value="4.Несдан">4.Несдан</option>
                <option value="5.На исправление">5.На исправление</option>
                <option value="Удален корректировкой">Удален корректировкой</option>
            </select>
        </div>

        <button type="submit" name="submit">Отправить</button>
    </form>

    <div class="table-container">
        

        <?php
        // Обработка XML
        $documents = array();
        $xmlFilePath = "./doc89c/status/статусыУПД.xml";
        $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder']: 'desc';

        if (file_exists($xmlFilePath)) {
            $xml = simplexml_load_file($xmlFilePath);
            foreach ($xml->СтатусДокумента as $item) {
                $documents[] = array(
                    'Документ' => (string)$item->Документ,
                    'Период'   => strtotime((string)$item->Период),
                    'Статус'   => (string)$item->Статус,
                    'Автор'    => (string)$item->Автор,
                );
            }
            usort($documents, function ($a,$b) use($sortOrder){
                $result = ($a['Период'] < $b['Период'])? -1: 1;
                return ($sortOrder == 'asc')? $result: -$result ;
            });

            /*
            array_sort_by_column($documents, 'Период', SORT_DESC);
            */
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            foreach (array_keys($documents[0]) as $column) {
                if ($column === 'Период'){
                    echo '<th><a href="?sortOrder=' . ($sortOrder == 'asc' ? 'desc' : 'asc') . '">' . $column . '</a></th>';
                } else {
                    echo '<th>' . $column . '</th>';
                }
            }
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($documents as $document) {
                echo '<tr>';
                foreach ($document as $key => $value) {
                    if ($key ==='Период'){
                        echo '<td>' . date('d-m-Y H:i:s', $value).'</td>';
                    }else{
                        echo '<td>'.$value.'</td>';
                    }

                }
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo 'Файл не найден.';
        }

        /*
        function array_sort_by_column(&$array, $column, $direction = SORT_ASC) {
            $reference_array = array();

            foreach ($array as $key => $row) {
                $reference_array[$key] = $row[$column];
            }

            array_multisort($reference_array, $direction, $array);
        }
        */

        ?>
        <script>
            const tableContainer = document.querySelector('.table-container');
            const tableElement = tableContainer.querySelector('table');
            // JavaScript для сохранения и восстановления значения select с использованием localStorage
            $(document).ready(function () {
                const selectElement = document.getElementById('selectText');
                const inputElement = document.getElementById('numberInput');
                // const tableContainer = document.querySelector('.table-container');
                // const tableElement = tableContainer.querySelector('table');
                // Получаем значение из localStorage при загрузке страницы
                const savedValue = localStorage.getItem('selectedStatus');
                if (savedValue) {
                    selectElement.value = savedValue; // Восстанавливаем выбранное значение
                }

                // Обработчик изменения значения в select
                selectElement.addEventListener('change', function(event) {
                    event.preventDefault(); // Предотвращаем стандартное действие браузера

                    // Сохраняем выбранное значение в localStorage
                    localStorage.setItem('selectedStatus', this.value);

                    // Переключаем фокус на input
                    inputElement.focus();
                });

                // const thElements = document.querySelectorAll('table th');
                // const thPeriodIndex = Array.from(thElements).findIndex((value, index) =>
                //     value.textContent.trim() === 'Период');
                //
                // thElements[thPeriodIndex].addEventListener('click', function() {
                //     sortTableByColumn('Период');
                // });
                //
                // // Функция для сортировки таблицы по колонке "период"
                // function sortTableByColumn(column) {
                //     const rows = Array.from(tableElement.querySelectorAll('tbody tr'));
                //
                //     rows.sort((rowA, rowB) => {
                //         const valueA = rowA.querySelectorAll(`td`)[thPeriodIndex]?.innerText || '';
                //         const valueB = rowB.querySelectorAll(`td`)[thPeriodIndex]?.innerText || '';
                //         const timesArrayA = valueA.replace(/[^0-9]/g, '-').split('-');
                //         const timesArrayB = valueB.replace(/[^0-9]/g, '-').split('-');
                //
                //         const dateA = new Date(timesArrayA[2],timesArrayA[1],timesArrayA[0],
                //             timesArrayA[3],timesArrayA[4],timesArrayA[5]);
                //         const dateB = new Date(timesArrayB[2],timesArrayB[1],timesArrayB[0],
                //             timesArrayB[3],timesArrayB[4],timesArrayB[5]);
                //         console.log(dateB - dateA);
                //         return dateB - dateA;
                //     });
                //
                //     tableElement.querySelector('tbody').innerHTML = '';
                //     rows.forEach(row => tableElement.querySelector('tbody').appendChild(row));
                // }
            });
            // const thElements = document.querySelectorAll('table th');
            // const thPeriodIndex = Array.from(thElements).findIndex((value, index) =>
            //     value.textContent.trim() === 'Период');
            //
            // thElements[thPeriodIndex].addEventListener('click', function() {
            //     sortTableByColumn('Период');
            // });

            // Функция для сортировки таблицы по колонке "период"
            // function sortTableByColumn(column) {
            //     const rows = Array.from(tableElement.querySelectorAll('tbody tr'));
            //
            //     rows.sort((rowA, rowB) => {
            //         const valueA = rowA.querySelectorAll(`td`)[thPeriodIndex]?.innerText || '';
            //         const valueB = rowB.querySelectorAll(`td`)[thPeriodIndex]?.innerText || '';
            //         const timesArrayA = valueA.replace(/[^0-9]/g, '-').split('-');
            //         const timesArrayB = valueB.replace(/[^0-9]/g, '-').split('-');
            //
            //         const dateA = new Date(timesArrayA[2],timesArrayA[1],timesArrayA[0],
            //             timesArrayA[3],timesArrayA[4],timesArrayA[5]);
            //         const dateB = new Date(timesArrayB[2],timesArrayB[1],timesArrayB[0],
            //             timesArrayB[3],timesArrayB[4],timesArrayB[5]);
            //         console.log(dateB - dateA);
            //         return dateB - dateA;
            //     });
            //
            //     tableElement.querySelector('tbody').innerHTML = '';
            //     rows.forEach(row => tableElement.querySelector('tbody').appendChild(row));
            // }
        </script>
    </div>
    </body>
    </html>


<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>