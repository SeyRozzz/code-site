/* ============================================
   GESTION DES ALERTES AVEC AUTO-FERMETURE
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss des alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Ajouter bouton de fermeture
        const closeBtn = document.createElement('span');
        closeBtn.className = 'alert-close';
        closeBtn.innerHTML = '×';
        closeBtn.style.cursor = 'pointer';
        alert.appendChild(closeBtn);

        closeBtn.addEventListener('click', function() {
            dismissAlert(alert);
        });

        // Auto-dismiss après 5 secondes (sauf erreurs)
        if (!alert.classList.contains('alert-error')) {
            setTimeout(() => {
                dismissAlert(alert);
            }, 5000);
        }
    });
});

function dismissAlert(element) {
    element.classList.add('dismissing');
    setTimeout(() => {
        element.remove();
    }, 300);
}

/* ============================================
   SYSTÈME DE CONFIRMATIONS AVANT SUPPRESSION
   ============================================ */

function confirmDelete(event, titre = "Êtes-vous sûr?", message = "Cette action ne peut pas être annulée.") {
    event.preventDefault();

    const modal = createConfirmModal(titre, message, () => {
        // Si l'utilisateur confirme, soumettre le formulaire
        if (event.target.tagName === 'BUTTON' || event.target.tagName === 'A') {
            if (event.target.tagName === 'BUTTON') {
                event.target.closest('form').submit();
            } else {
                window.location.href = event.target.href;
            }
        } else if (event.target.closest('form')) {
            event.target.closest('form').submit();
        }
    });

    document.body.appendChild(modal);
}

function confirmDeleteProject(event, projectName) {
    event.preventDefault();
    
    confirmDelete(
        event,
        "Supprimer le projet?",
        `Êtes-vous sûr de vouloir supprimer le projet "${projectName}"? Tous les arbres seront également supprimés. Cette action ne peut pas être annulée.`
    );
}

function confirmDeleteUser(event, userName) {
    event.preventDefault();
    
    confirmDelete(
        event,
        "Supprimer l'utilisateur?",
        `Êtes-vous sûr de vouloir supprimer ${userName}? Cette action ne peut pas être annulée.`
    );
}

function confirmDeleteTree(event, essenceName = "cet arbre") {
    event.preventDefault();
    
    confirmDelete(
        event,
        "Supprimer l'arbre?",
        `Êtes-vous sûr de vouloir supprimer ${essenceName}? Cette action ne peut pas être annulée.`
    );
}

/* ============================================
   HELPER: CRÉER MODAL DE CONFIRMATION
   ============================================ */

function createConfirmModal(title, message, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'confirm-modal';

    const content = document.createElement('div');
    content.className = 'confirm-modal-content';

    const titleEl = document.createElement('h3');
    titleEl.textContent = title;

    const messageEl = document.createElement('p');
    messageEl.textContent = message;

    const actionsDiv = document.createElement('div');
    actionsDiv.className = 'confirm-modal-actions';

    const cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.className = 'btn-cancel';
    cancelBtn.textContent = 'Annuler';
    cancelBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        modal.classList.add('dismissing');
        setTimeout(() => modal.remove(), 300);
    };

    const confirmBtn = document.createElement('button');
    confirmBtn.type = 'button';
    confirmBtn.className = 'btn-confirm';
    confirmBtn.textContent = 'Supprimer';
    confirmBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        modal.remove();
        onConfirm();
    };

    actionsDiv.appendChild(cancelBtn);
    actionsDiv.appendChild(confirmBtn);

    content.appendChild(titleEl);
    content.appendChild(messageEl);
    content.appendChild(actionsDiv);

    modal.appendChild(content);

    // Fermer au clic en dehors
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('dismissing');
            setTimeout(() => modal.remove(), 300);
        }
    });

    // Fermer avec Echap
    const closeOnEscape = (e) => {
        if (e.key === 'Escape') {
            modal.classList.add('dismissing');
            setTimeout(() => modal.remove(), 300);
            document.removeEventListener('keydown', closeOnEscape);
        }
    };
    document.addEventListener('keydown', closeOnEscape);

    return modal;
}

/* ============================================
   AFFICHER ALERTE DYNAMIQUEMENT
   ============================================ */

function showAlert(message, type = 'info', autoClose = true) {
    const container = document.querySelector('main') || document.body;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = message;

    // Ajouter bouton fermeture
    const closeBtn = document.createElement('span');
    closeBtn.className = 'alert-close';
    closeBtn.innerHTML = '×';
    closeBtn.style.cursor = 'pointer';

    closeBtn.addEventListener('click', function() {
        dismissAlert(alert);
    });

    alert.appendChild(closeBtn);
    container.insertBefore(alert, container.firstChild);

    // Auto-fermeture
    if (autoClose && type !== 'error') {
        setTimeout(() => {
            dismissAlert(alert);
        }, 5000);
    }
}

/* ============================================
   LOADING STATE
   ============================================ */

function setLoading(button, isLoading = true) {
    if (isLoading) {
        button.disabled = true;
        button.dataset.originalText = button.textContent;
        button.innerHTML = '<span class="load-spinner"></span> Traitement...';
    } else {
        button.disabled = false;
        button.textContent = button.dataset.originalText || 'Valider';
    }
}

/* ============================================
   UTILITAIRES FORMULAIRES
   ============================================ */

// Ajouter validation visuelle au formulaires
document.addEventListener('submit', function(e) {
    const form = e.target;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    let isValid = true;
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#FF6B6B';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });

    if (!isValid) {
        e.preventDefault();
        showAlert('Veuillez remplir tous les champs obligatoires', 'warning');
    }
});

/* ============================================
   ACTIONS DESTRUCTRICES - ATTACHEMENT ÉVÉNEMENTS
   ============================================ */

// Attacher confirmations aux boutons destructeurs
document.addEventListener('click', function(e) {
    // Suppression d'arbre
    if (e.target.classList.contains('delete-tree')) {
        const essence = e.target.dataset.essence || 'cet arbre';
        confirmDeleteTree(e, essence);
    }

    // Suppression de projet
    if (e.target.classList.contains('delete-project')) {
        const projectName = e.target.dataset.project || 'ce projet';
        confirmDeleteProject(e, projectName);
    }

    // Suppression d'utilisateur
    if (e.target.classList.contains('delete-user')) {
        const userName = e.target.dataset.user || 'cet utilisateur';
        confirmDeleteUser(e, userName);
    }
});

/* ============================================
   GESTION CHANGEMENT DE RÔLE
   ============================================ */

function confirmRoleChange(event, userName, newRole) {
    event.preventDefault();

    const roleLabels = {
        'forestier': 'Forestier',
        'admin': 'Administrateur',
        'superadmin': 'Super Administrateur'
    };

    const modal = createConfirmModal(
        `Changer le rôle de ${userName}?`,
        `Êtes-vous sûr de vouloir définir le rôle à "${roleLabels[newRole] || newRole}"?`,
        () => {
            event.target.closest('form').submit();
        }
    );

    document.body.appendChild(modal);
}

/* ============================================
   RESPONSIVE - Adaptations Mobile
   ============================================ */

// Fermer confirmations au resize (très large écran)
window.addEventListener('resize', function() {
    const modals = document.querySelectorAll('.confirm-modal');
    if (window.innerWidth > 1200) {
        // On peut laisser les modals ouvertes sur desktop
    }
});

/* ============================================
   EXPORT UTILS - Pour PDF/Excel/etc
   ============================================ */

function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    const rows = Array.from(table.querySelectorAll('tr'));
    const csvContent = rows.map(row => {
        return Array.from(row.querySelectorAll('td, th'))
            .map(cell => `"${cell.textContent.trim()}"`)
            .join(',');
    }).join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function printPage() {
    window.print();
}
