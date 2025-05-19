document.addEventListener('DOMContentLoaded', function() {
  // Asigură-te că toate listele numerotate din implementări sunt formatate corect
  const implementationLists = document.querySelectorAll('.activity-implementation ol');
  
  implementationLists.forEach(list => {
    const items = list.querySelectorAll('li');
    items.forEach((item, index) => {
      if (!item.querySelector('strong')) {
        const text = item.innerHTML;
        const firstColon = text.indexOf(':');
        if (firstColon !== -1) {
          item.innerHTML = `<strong>${text.substring(0, firstColon + 1)}</strong>${text.substring(firstColon + 1)}`;
        }
      }
    });
  });
  
  // Asigură-te că toate listele cu bullets din resurse și adaptări sunt formatate corect
  const resourcesLists = document.querySelectorAll('.activity-resources ul, .activity-adaptations ul');
  
  resourcesLists.forEach(list => {
    const items = list.querySelectorAll('li');
    items.forEach(item => {
      if (!item.querySelector('i')) {
        item.innerHTML = `<i class="fas fa-check-circle" style="color: var(--primary-color); margin-right: 5px;"></i> ${item.innerHTML}`;
      }
    });
  });
});