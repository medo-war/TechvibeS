document.addEventListener('DOMContentLoaded', function() {
    const captchaElement = document.getElementById('captchaElement');
    const captchaTarget = document.getElementById('captchaTarget');
    const captchaValid = document.getElementById('captchaValid');
    const loginForm = document.getElementById('loginForm');
    
    // Activation du drag
    captchaElement.draggable = true;
    
    // Événements Drag
    captchaElement.addEventListener('dragstart', (e) => {
      e.dataTransfer.setData('text/plain', 'captcha');
      setTimeout(() => { captchaElement.style.display = 'none'; }, 0);
    });
  
    // Événements Drop
    captchaTarget.addEventListener('dragover', (e) => {
      e.preventDefault();
      captchaTarget.style.borderColor = '#4CAF50';
    });
  
    captchaTarget.addEventListener('drop', (e) => {
      e.preventDefault();
      if (e.dataTransfer.getData('text/plain') === 'captcha') {
        captchaTarget.innerHTML = '🔒';
        captchaValid.value = '1';
        captchaTarget.style.backgroundColor = '#e8f5e9';
      }
    });
  
    // Gestion du formulaire
    loginForm.addEventListener('submit', function(e) {
      if (captchaValid.value !== '1') {
        e.preventDefault();
        document.getElementById('captchaContainer').style.display = 'block';
        alert('Veuillez compléter la vérification CAPTCHA');
      }
    });
  });