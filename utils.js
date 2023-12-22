function clearResults() {
    const resultContainer = document.getElementById('searchContainer');
    while (resultContainer.firstChild) {
        resultContainer.removeChild(resultContainer.firstChild);
    }
}