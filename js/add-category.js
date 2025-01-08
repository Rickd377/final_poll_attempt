document.getElementById('add-option-btn').addEventListener('click', function() {
  const optionsContainer = document.getElementById('options-container');
  const optionCount = optionsContainer.querySelectorAll('input').length;
  if (optionCount < 6) {
    const newOption = document.createElement('input');
    newOption.type = 'text';
    newOption.name = 'options[]';
    newOption.placeholder = 'Option ' + (optionCount + 1);
    optionsContainer.appendChild(newOption);
  } else {
    alert('You can only add up to 6 options.');
  }
});