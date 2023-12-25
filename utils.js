function clearResults() {
    const resultContainer = document.getElementById('searchContainer');
    while (resultContainer.firstChild) {
        resultContainer.removeChild(resultContainer.firstChild);
    }
}

function handlerClearInput() {
    const inputElement = document.getElementById('textInput');
    inputElement.value = '';
}