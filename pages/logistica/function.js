function populateSelect(mySelect, data) {
   const selectElement = document.getElementById(mySelect);
   selectElement.innerHTML = ''; // Clear existing options (optional)

   // Add a default "Select an option" if desired
   const defaultOption = document.createElement('option');
   defaultOption.value = '';
   defaultOption.textContent = 'Selecione uma opção';
   selectElement.appendChild(defaultOption);

   data.forEach(item => {
       const option = document.createElement('option');
       option.value = item.value; // Assuming 'value' property in your data
       option.textContent = item.text; // Assuming 'text' property in your data
       selectElement.appendChild(option);
   });
}