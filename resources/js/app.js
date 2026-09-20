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
