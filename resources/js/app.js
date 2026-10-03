const modal = document.querySelector('#appointment-modal');

if (modal) {
    const form = document.querySelector('#appointment-form');
    const methodField = document.querySelector('#form-method');
    const appointmentIdField = document.querySelector('#appointment-id');
    const modalTitle = document.querySelector('#modal-title');
    const submitButton = document.querySelector('#submit-button');
    const projectSelect = document.querySelector('#project-id');
    const newProjectInput = document.querySelector('#new-project-name');
    const existingProjectFields = document.querySelector('#existing-project-fields');
    const newProjectFields = document.querySelector('#new-project-fields');
    const durationField = document.querySelector('#duration');
    const projectSearch = document.querySelector('[data-project-search]');
    const projectOptions = [...projectSelect.options].map((option) => ({ value: option.value, label: option.textContent }));
    let triggerToRestore = null;

    const field = (name) => form.elements.namedItem(name);

    const showExistingProject = () => {
        existingProjectFields.classList.remove('hidden');
        newProjectFields.classList.add('hidden');
        projectSelect.disabled = false;
        newProjectInput.disabled = true;
        newProjectInput.value = '';
    };

    const showNewProject = () => {
        existingProjectFields.classList.add('hidden');
        newProjectFields.classList.remove('hidden');
        projectSelect.disabled = true;
        projectSelect.value = '';
        newProjectInput.disabled = false;
        window.setTimeout(() => newProjectInput.focus(), 0);
    };

    const fillForm = (values = {}) => {
        projectSearch.value = '';
        projectSelect.replaceChildren(...projectOptions.map(({ value, label }) => new Option(label, value)));
        field('date').value = values.date ?? window.agendaData.dataSelecionada;
        field('duration').value = values.duration ?? '';
        field('project_task').value = values.project_task ?? '';
        field('occurrence').value = values.occurrence ?? '';
        field('internal_description').value = values.internal_description ?? '';
        field('entry_type').value = values.entry_type ?? 'work';
        field('owner').value = values.owner ?? '';

        if (values.new_project_name) {
            showNewProject();
            newProjectInput.value = values.new_project_name;
        } else {
            showExistingProject();
            projectSelect.value = values.project_id ?? '';
        }
    };

    const openModal = (mode, values = {}, trigger = null) => {
        triggerToRestore = trigger;
        form.reset();
        fillForm(values);

        if (mode === 'edit') {
            const id = String(values.id);
            modalTitle.textContent = 'Editar apontamento';
            submitButton.textContent = 'Atualizar';
            appointmentIdField.value = id;
            methodField.disabled = false;
            form.action = modal.dataset.updateUrl.replace('__ID__', id);
        } else {
            modalTitle.textContent = 'Novo apontamento';
            submitButton.textContent = 'Salvar';
            appointmentIdField.value = '';
            methodField.disabled = true;
            form.action = modal.dataset.storeUrl;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        window.setTimeout(() => durationField.focus(), 0);
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        triggerToRestore?.focus();
    };

    document.querySelectorAll('[data-open-create]').forEach((button) => {
        button.addEventListener('click', () => openModal('create', {}, button));
    });

    document.querySelectorAll('[data-edit-appointment]').forEach((button) => {
        button.addEventListener('click', () => {
            const appointment = window.agendaData.apontamentos[button.dataset.editAppointment];
            openModal('edit', appointment, button);
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.querySelector('[data-show-new-project]').addEventListener('click', showNewProject);
    document.querySelector('[data-show-existing-project]').addEventListener('click', () => {
        showExistingProject();
        projectSelect.focus();
    });

    projectSearch.addEventListener('input', () => {
        const selectedValue = projectSelect.value;
        const search = projectSearch.value.trim().toLocaleLowerCase('pt-BR');
        const matches = projectOptions.filter(({ value, label }) => ! value || label.toLocaleLowerCase('pt-BR').includes(search));
        projectSelect.replaceChildren(...matches.map(({ value, label }) => new Option(label, value)));
        projectSelect.value = matches.some(({ value }) => value === selectedValue) ? selectedValue : '';

        if (search && matches.filter(({ value }) => value).length === 1) {
            projectSelect.value = matches.find(({ value }) => value)?.value ?? '';
        }
    });

    const normalizeDuration = () => {
        const input = durationField.value.trim().replace(',', '.');
        if (/^\d{1,3}:\d{1,2}$/.test(input)) {
            const [hours, minutes] = input.split(':').map(Number);
            if (minutes < 60) durationField.value = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
        } else if (/^\d{1,3}(\.\d{1,2})?$/.test(input)) {
            const decimalHours = input.includes('.') ? Number(input) : Number(input) / 100;
            const totalMinutes = Math.round(decimalHours * 60);
            durationField.value = `${String(Math.floor(totalMinutes / 60)).padStart(2, '0')}:${String(totalMinutes % 60).padStart(2, '0')}`;
        }
    };
    durationField.addEventListener('blur', normalizeDuration);

    form.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && event.target.matches('input:not([type="date"]), select')) {
            event.preventDefault();
            normalizeDuration();
            form.requestSubmit();
        }
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && ! modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    form.addEventListener('submit', () => {
        normalizeDuration();
        submitButton.disabled = true;
        submitButton.textContent = appointmentIdField.value ? 'Atualizando...' : 'Salvando...';
    });

    document.querySelectorAll('[data-delete-form]').forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            if (! window.confirm('Deseja realmente excluir este apontamento?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelector('[data-date-picker]').addEventListener('change', (event) => {
        event.currentTarget.form.submit();
    });

    if (window.agendaData.validacao.possuiErros) {
        const values = window.agendaData.validacao.valores;
        openModal(values.id ? 'edit' : 'create', values);
    }
}

document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (! window.confirm(form.dataset.confirmDelete)) event.preventDefault();
    });
});

document.querySelectorAll('[data-auto-submit]').forEach((select) => {
    select.addEventListener('change', () => select.form.requestSubmit());
});

document.querySelectorAll('#notes details[id^="note-"]').forEach((note) => {
    const applyFilter = () => {
        const priority = note.querySelector('[data-priority-filter]').value;
        const search = note.querySelector('[data-task-search]').value.trim().toLocaleLowerCase('pt-BR');
        note.querySelectorAll('[data-kanban-card]').forEach((card) => {
            card.hidden = Boolean((priority && card.dataset.priority !== priority) || (search && ! card.dataset.search.includes(search)));
        });
    };
    note.querySelector('[data-priority-filter]').addEventListener('change', applyFilter);
    note.querySelector('[data-task-search]').addEventListener('input', applyFilter);

    let draggedCard = null;
    note.querySelectorAll('[data-kanban-card][draggable="true"]').forEach((card) => {
        card.addEventListener('dragstart', (event) => {
            draggedCard = card;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', card.dataset.moveUrl);
        });
        card.addEventListener('dragend', () => { draggedCard = null; });
    });

    note.querySelectorAll('[data-kanban-column]').forEach((column) => {
        column.addEventListener('dragover', (event) => {
            if (draggedCard) event.preventDefault();
        });
        column.addEventListener('drop', async (event) => {
            if (! draggedCard) return;
            event.preventDefault();
            const status = column.dataset.kanbanColumn;
            try {
                const response = await fetch(draggedCard.dataset.moveUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ status }),
                });
                if (! response.ok) throw new Error('Falha ao mover pendência');
                window.location.search = new URLSearchParams({ date: window.agendaData.dataSelecionada, notes: note.id.replace('note-', '') });
            } catch {
                window.alert('Não foi possível mover a pendência. Tente novamente.');
            }
        });
    });
});
