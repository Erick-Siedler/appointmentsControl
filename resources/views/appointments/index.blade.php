<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Controle de Apontamentos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-5 sm:px-6 lg:px-8">
            <div class="flex size-10 items-center justify-center rounded-lg bg-blue-700 text-white shadow-sm" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v3m8-3v3M3 9h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
                    <path stroke-linecap="round" d="M8 13h3m-3 4h3m3-4h2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">Agenda diária</p>
                <h1 class="text-xl font-semibold tracking-tight text-slate-950">Controle de Apontamentos</h1>
            </div>
        </div>
    </header>

    <main class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm" role="status">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5" aria-labelledby="data-selecionada">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('appointments.index', ['date' => $previousDate]) }}" class="botao-icone" aria-label="Dia anterior" title="Dia anterior">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                        </svg>
                    </a>

                    <form action="{{ route('appointments.index') }}" method="GET" class="min-w-0 flex-1 sm:flex-none">
                        <label for="date-picker" class="sr-only">Selecionar data</label>
                        <input id="date-picker" name="date" type="date" value="{{ $selectedDate->toDateString() }}" class="campo-data" data-date-picker>
                    </form>

                    <a href="{{ route('appointments.index', ['date' => $nextDate]) }}" class="botao-icone" aria-label="Próximo dia" title="Próximo dia">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>

                    <a href="{{ route('appointments.index', ['date' => today()->toDateString()]) }}" class="botao-secundario ml-1">Hoje</a>
                </div>

                <div class="min-w-0 lg:text-right">
                    <p id="data-selecionada" class="truncate text-lg font-semibold text-slate-950">
                        {{ ucfirst($selectedDate->locale('pt_BR')->translatedFormat('l, d \d\e F \d\e Y')) }}
                    </p>
                    <p class="text-sm text-slate-500">Todos os apontamentos desta data</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Resumo do dia">
            <article class="cartao-resumo">
                <p class="rotulo-resumo">Apontamentos</p>
                <p class="valor-resumo">{{ $summary['count'] }}</p>
            </article>
            <article class="cartao-resumo">
                <p class="rotulo-resumo">Trabalho</p>
                <p class="valor-resumo">{{ $summary['work'] }}</p>
            </article>
            <article class="cartao-resumo border-amber-200">
                <p class="rotulo-resumo text-amber-700">Hora extra</p>
                <p class="valor-resumo text-amber-700">{{ $summary['overtime'] }}</p>
            </article>
            <article class="cartao-resumo border-blue-200 bg-blue-50/60">
                <p class="rotulo-resumo text-blue-700">Total do dia</p>
                <p class="valor-resumo text-blue-800">{{ $summary['total'] }}</p>
            </article>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="lista-apontamentos">
            <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div>
                    <h2 id="lista-apontamentos" class="text-lg font-semibold text-slate-950">Apontamentos do dia</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Acompanhe o tempo registrado por projeto.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('appointments.export', ['date' => $selectedDate->toDateString()]) }}" class="botao-secundario justify-center">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/>
                        </svg>
                        Exportar Excel
                    </a>
                    <button type="button" class="botao-primario justify-center" data-open-create>
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                        </svg>
                        Novo apontamento
                    </button>
                </div>
            </div>

            @if ($appointments->isEmpty())
                <div class="flex flex-col items-center px-6 py-16 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-slate-100 text-slate-500" aria-hidden="true">
                        <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v3m8-3v3M3 9h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
                            <path stroke-linecap="round" d="M9 15h6"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-slate-900">Nenhum apontamento registrado neste dia.</h3>
                    <p class="mt-1 max-w-md text-sm text-slate-500">Registre o primeiro período de trabalho para começar o acompanhamento.</p>
                    <button type="button" class="botao-primario mt-5" data-open-create>
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                        </svg>
                        Criar apontamento
                    </button>
                </div>
            @else
                <div class="divide-y divide-slate-200">
                    @foreach ($appointments as $appointment)
                        <article class="p-4 transition-colors hover:bg-slate-50/70 sm:p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start">
                                <div class="flex shrink-0 items-center justify-between gap-3 lg:w-28 lg:flex-col lg:items-start">
                                    <p class="font-mono text-2xl font-bold tracking-tight text-blue-700">{{ $appointment->formattedDuration() }}</p>
                                    <span class="{{ $appointment->entry_type === 'overtime' ? 'etiqueta-hora-extra' : 'etiqueta-trabalho' }}">
                                        {{ $appointment->entryTypeLabel() }}
                                    </span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:gap-3">
                                        <h3 class="truncate text-base font-semibold text-slate-950">{{ $appointment->project->name }}</h3>
                                        @if ($appointment->project_task)
                                            <p class="truncate text-sm font-medium text-slate-600">{{ $appointment->project_task }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                                        <div>
                                            <p class="rotulo-detalhe">Ocorrência</p>
                                            <p class="texto-detalhe">{{ $appointment->occurrence ?: 'Não informada' }}</p>
                                        </div>
                                        <div>
                                            <p class="rotulo-detalhe">Responsável</p>
                                            <p class="texto-detalhe">{{ $appointment->owner ?: 'Não informado' }}</p>
                                        </div>
                                    </div>

                                    @if ($appointment->internal_description)
                                        <div class="mt-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-2.5">
                                            <p class="rotulo-detalhe">Descrição interna</p>
                                            <p class="mt-1 whitespace-pre-line text-sm leading-5 text-slate-700">{{ $appointment->internal_description }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex shrink-0 items-center gap-2 lg:pl-3">
                                    <button type="button" class="botao-acao" data-edit-appointment="{{ $appointment->id }}">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.7 6.3 3 3M4 20l3.5-.7L19 7.8a2.1 2.1 0 0 0-3-3L4.7 16.5 4 20Z"/>
                                        </svg>
                                        Editar
                                    </button>
                                    <form method="POST" action="{{ route('appointments.destroy', ['appointment' => $appointment, 'date' => $selectedDate->toDateString()]) }}" data-delete-form>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="botao-excluir">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16m-10 4v6m4-6v6M9 7l1-3h4l1 3m-9 0 1 14h10l1-14"/>
                                            </svg>
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section id="notes" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="daily-notes-title">
            <div class="border-b border-slate-200 px-4 py-5 sm:px-5">
                <h2 id="daily-notes-title" class="text-lg font-semibold text-slate-950">Anotações do dia</h2>
                <p class="mt-0.5 text-sm text-slate-500">Registre observações e acompanhe as pendências de cada anotação.</p>
            </div>

            <div class="space-y-5 p-4 sm:p-5">
                @if ($errors->notes->any() || $errors->tasks->any())
                    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        @foreach ($errors->notes->all() as $message)<p>{{ $message }}</p>@endforeach
                        @foreach ($errors->tasks->all() as $message)<p>{{ $message }}</p>@endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('daily-notes.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}">
                    <label for="note-body" class="block text-sm font-semibold text-slate-700">Nova anotação</label>
                    <textarea id="note-body" name="body" rows="3" maxlength="10000" required class="campo-formulario resize-y" placeholder="Escreva uma observação para este dia...">{{ old('body') }}</textarea>
                    @error('body', 'notes') <p class="mensagem-erro">{{ $message }}</p> @enderror
                    <button type="submit" class="botao-primario">Adicionar Anotação</button>
                </form>

                @forelse ($dailyNotes as $note)
                    @php($canEditNote = $noteAuthorToken === $note->author_token)
                    <details id="note-{{ $note->id }}" class="rounded-lg border border-slate-200 bg-slate-50" @if (request('notes') == $note->id) open @endif>
                        <summary class="cursor-pointer list-none px-4 py-4 focus-visible:outline-2 focus-visible:outline-blue-600 sm:px-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <p class="max-w-3xl whitespace-pre-line text-sm leading-6 text-slate-800">{{ $note->body }}</p>
                                <span class="shrink-0 text-xs font-medium text-slate-500">{{ $note->created_at->format('d/m/Y \à\s H:i') }}</span>
                            </div>
                            <span class="mt-2 block text-xs font-semibold text-blue-700">{{ $note->pendingTasks->count() }} pendência(s) · Abrir checklist Kanban</span>
                        </summary>

                        <div class="space-y-5 border-t border-slate-200 bg-white p-4 sm:p-5">
                            @if ($canEditNote)
                                <div class="flex flex-wrap gap-3">
                                    <details class="min-w-0 flex-1">
                                        <summary class="cursor-pointer text-sm font-semibold text-blue-700">Editar anotação</summary>
                                        <form method="POST" action="{{ route('daily-notes.update', $note) }}" class="mt-2 space-y-2">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="body" rows="3" maxlength="10000" required class="campo-formulario resize-y">{{ $note->body }}</textarea>
                                            <button type="submit" class="botao-secundario">Salvar anotação</button>
                                        </form>
                                    </details>
                                    <form method="POST" action="{{ route('daily-notes.destroy', $note) }}" data-confirm-delete="Excluir esta anotação e todas as suas pendências?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="botao-excluir">Excluir anotação</button>
                                    </form>
                                </div>
                            @endif

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <h3 class="font-semibold text-slate-900">Checklist de pendências</h3>
                                    <p class="text-xs text-slate-500">Arraste os cards entre colunas ou altere o status no próprio card.</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <label class="text-xs font-semibold text-slate-600">Prioridade
                                        <select class="campo-formulario mt-1" data-priority-filter>
                                            <option value="">Todas</option>
                                            @foreach ($taskPriorities as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="text-xs font-semibold text-slate-600">Pesquisar
                                        <input type="search" class="campo-formulario mt-1" placeholder="Palavra-chave" data-task-search>
                                    </label>
                                </div>
                            </div>

                            @if ($canEditNote)
                                <details class="rounded-lg border border-blue-200 bg-blue-50/50 p-3">
                                    <summary class="cursor-pointer text-sm font-semibold text-blue-700">+ Nova pendência</summary>
                                    <form method="POST" action="{{ route('pending-tasks.store', $note) }}" class="mt-3 grid gap-3 sm:grid-cols-2">
                                        @csrf
                                        <label class="text-sm font-medium text-slate-700 sm:col-span-2">Título <input name="title" required maxlength="255" class="campo-formulario mt-1"></label>
                                        <label class="text-sm font-medium text-slate-700 sm:col-span-2">Descrição <textarea name="description" rows="2" maxlength="5000" class="campo-formulario mt-1"></textarea></label>
                                        <label class="text-sm font-medium text-slate-700">Status <select name="status" class="campo-formulario mt-1">@foreach ($taskStatuses as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></label>
                                        <label class="text-sm font-medium text-slate-700">Prioridade <select name="priority" class="campo-formulario mt-1">@foreach ($taskPriorities as $key => $label)<option value="{{ $key }}" @selected($key === 'normal')>{{ $label }}</option>@endforeach</select></label>
                                        <label class="text-sm font-medium text-slate-700">Vencimento <input name="due_date" type="date" class="campo-formulario mt-1"></label>
                                        <label class="text-sm font-medium text-slate-700">Responsável <input name="assignee" maxlength="255" class="campo-formulario mt-1"></label>
                                        <button type="submit" class="botao-primario w-fit sm:col-span-2">Adicionar pendência</button>
                                    </form>
                                </details>
                            @endif

                            <div class="flex gap-3 overflow-x-auto pb-3" data-kanban-board>
                                @foreach ($taskStatuses as $statusKey => $statusLabel)
                                    <section class="w-72 shrink-0 rounded-lg border border-slate-200 bg-slate-50" aria-label="{{ $statusLabel }}" data-kanban-column="{{ $statusKey }}">
                                        <div class="flex items-center justify-between border-b border-slate-200 px-3 py-2.5">
                                            <h4 class="text-sm font-semibold text-slate-800">{{ $statusLabel }}</h4>
                                            <span class="text-xs text-slate-500">{{ $note->pendingTasks->where('status', $statusKey)->count() }}</span>
                                        </div>
                                        <div class="min-h-24 space-y-2 p-2" data-kanban-dropzone>
                                            @foreach ($note->pendingTasks->where('status', $statusKey) as $task)
                                                <article class="rounded-md border border-slate-200 bg-white p-3 shadow-sm" data-kanban-card data-priority="{{ $task->priority }}" data-search="{{ Str::lower($task->title.' '.$task->description.' '.$task->assignee) }}" data-move-url="{{ route('pending-tasks.move', $task) }}" @if ($canEditNote) draggable="true" @endif>
                                                    <div class="flex items-start justify-between gap-2">
                                                        <h5 class="text-sm font-semibold text-slate-900">{{ $task->title }}</h5>
                                                        <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ ['low' => 'bg-emerald-100 text-emerald-800', 'normal' => 'bg-blue-100 text-blue-800', 'high' => 'bg-orange-100 text-orange-800', 'urgent' => 'bg-red-100 text-red-800'][$task->priority] }}">{{ $taskPriorities[$task->priority] }}</span>
                                                    </div>
                                                    @if ($task->description)<p class="mt-2 break-words whitespace-pre-line text-xs leading-5 text-slate-600">{{ $task->description }}</p>@endif
                                                    @if ($task->due_date || $task->assignee)
                                                        <p class="mt-2 text-xs text-slate-500">@if ($task->due_date) Vence {{ $task->due_date->format('d/m/Y') }} @endif @if ($task->assignee) · {{ $task->assignee }} @endif</p>
                                                    @endif
                                                    <p class="mt-2 text-xs font-medium text-slate-500">{{ $task->checklistItems->where('is_done', true)->count() }}/{{ $task->checklistItems->count() }} subtarefas</p>
                                                    @if ($canEditNote)
                                                        <form method="POST" action="{{ route('pending-tasks.move', $task) }}" class="mt-2">
                                                            @csrf
                                                            @method('PATCH')
                                                            <label class="sr-only" for="task-status-{{ $task->id }}">Status de {{ $task->title }}</label>
                                                            <select id="task-status-{{ $task->id }}" name="status" class="campo-formulario text-xs" data-auto-submit>
                                                                @foreach ($taskStatuses as $key => $label)<option value="{{ $key }}" @selected($task->status === $key)>{{ $label }}</option>@endforeach
                                                            </select>
                                                        </form>
                                                    @endif
                                                    <details class="mt-2 border-t border-slate-100 pt-2">
                                                        <summary class="cursor-pointer text-xs font-semibold text-blue-700">Detalhes e subtarefas</summary>
                                                        @if ($canEditNote)
                                                            <form method="POST" action="{{ route('pending-tasks.update', $task) }}" class="mt-2 space-y-2">
                                                                @csrf
                                                                @method('PATCH')
                                                                <label class="block text-xs">Título <input name="title" value="{{ $task->title }}" required maxlength="255" class="campo-formulario mt-1"></label>
                                                                <label class="block text-xs">Descrição <textarea name="description" rows="3" maxlength="5000" class="campo-formulario mt-1">{{ $task->description }}</textarea></label>
                                                                <label class="block text-xs">Prioridade <select name="priority" class="campo-formulario mt-1">@foreach ($taskPriorities as $key => $label)<option value="{{ $key }}" @selected($task->priority === $key)>{{ $label }}</option>@endforeach</select></label>
                                                                <label class="block text-xs">Vencimento <input name="due_date" type="date" value="{{ $task->due_date?->toDateString() }}" class="campo-formulario mt-1"></label>
                                                                <label class="block text-xs">Responsável <input name="assignee" value="{{ $task->assignee }}" maxlength="255" class="campo-formulario mt-1"></label>
                                                                <input type="hidden" name="status" value="{{ $task->status }}">
                                                                <button type="submit" class="botao-secundario">Salvar card</button>
                                                            </form>
                                                        @endif
                                                        <ul class="mt-3 space-y-2">
                                                            @foreach ($task->checklistItems as $item)
                                                                <li class="flex items-start gap-2 text-xs text-slate-700">
                                                                    @if ($canEditNote)
                                                                        <form method="POST" action="{{ route('checklist-items.update', $item) }}">
                                                                            @csrf @method('PATCH')
                                                                            <input type="hidden" name="is_done" value="{{ $item->is_done ? 0 : 1 }}">
                                                                            <button type="submit" class="size-5 rounded border border-slate-300" aria-label="{{ $item->is_done ? 'Desmarcar' : 'Concluir' }} {{ $item->text }}">{{ $item->is_done ? '✓' : '' }}</button>
                                                                        </form>
                                                                    @else
                                                                        <span aria-hidden="true">{{ $item->is_done ? '✓' : '○' }}</span>
                                                                    @endif
                                                                    <span class="flex-1 {{ $item->is_done ? 'line-through text-slate-400' : '' }}">{{ $item->text }}</span>
                                                                    @if ($canEditNote)
                                                                        <form method="POST" action="{{ route('checklist-items.destroy', $item) }}">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="text-red-600" aria-label="Excluir {{ $item->text }}">×</button>
                                                                        </form>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                        @if ($canEditNote)
                                                            <form method="POST" action="{{ route('checklist-items.store', $task) }}" class="mt-3 flex gap-1">
                                                                @csrf
                                                                <input name="text" maxlength="255" required class="campo-formulario min-w-0" placeholder="Nova subtarefa" aria-label="Nova subtarefa">
                                                                <button type="submit" class="botao-secundario">+</button>
                                                            </form>
                                                            <form method="POST" action="{{ route('pending-tasks.destroy', $task) }}" class="mt-3" data-confirm-delete="Excluir esta pendência?">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-xs font-semibold text-red-700">Excluir pendência</button>
                                                            </form>
                                                        @endif
                                                    </details>
                                                </article>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    </details>
                @empty
                    <p class="rounded-lg border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">Nenhuma anotação nesta data.</p>
                @endforelse
            </div>
        </section>
    </main>

    <div id="appointment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/55 p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title" data-store-url="{{ route('appointments.store') }}" data-update-url="{{ route('appointments.update', ['appointment' => '__ID__']) }}">
        <div class="flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl border border-slate-300 bg-white shadow-2xl" data-modal-panel>
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-blue-700">Registro de tempo</p>
                    <h2 id="modal-title" class="mt-1 text-xl font-semibold text-slate-950">Novo apontamento</h2>
                </div>
                <button type="button" class="botao-fechar" data-close-modal aria-label="Fechar formulário" title="Fechar">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>

            <form id="appointment-form" method="POST" action="{{ route('appointments.store') }}" class="min-h-0 overflow-y-auto">
                @csrf
                <input id="form-method" type="hidden" name="_method" value="PUT" disabled>
                <input id="appointment-id" type="hidden" name="appointment_id" value="{{ old('appointment_id') }}">

                <div class="flex flex-col gap-5 px-5 py-5 sm:px-6">
                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                            <p class="font-semibold">Revise os campos destacados.</p>
                            <p class="mt-0.5">Não foi possível salvar o apontamento.</p>
                        </div>
                    @endif

                    <div class="grupo-campo">
                        <label for="appointment-date" class="rotulo-campo">Data <span aria-hidden="true">*</span></label>
                        <div class="conteudo-campo">
                            <input id="appointment-date" name="date" type="date" class="campo-formulario @error('date') campo-invalido @enderror" value="{{ old('date', $selectedDate->toDateString()) }}" required>
                            @error('date') <p class="mensagem-erro">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grupo-campo">
                        <label for="duration" class="rotulo-campo">Duração <span aria-hidden="true">*</span></label>
                        <div class="conteudo-campo">
                            <input id="duration" name="duration" type="text" inputmode="numeric" autocomplete="off" placeholder="02:30" pattern="\d{1,3}:[0-5]\d" class="campo-formulario @error('duration') campo-invalido @enderror" value="{{ old('duration') }}" required>
                            <p class="mt-1.5 text-xs text-slate-500">Use HH:MM ou horas decimais. Ex.: 125 ou 1,25 vira 01:15.</p>
                            @error('duration') <p class="mensagem-erro">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grupo-campo items-start">
                        <label for="project-id" class="rotulo-campo sm:pt-2.5">Projeto <span aria-hidden="true">*</span></label>
                        <div class="conteudo-campo">
                            <div id="existing-project-fields">
                                <label for="project-search" class="sr-only">Buscar projeto</label>
                                <input id="project-search" type="search" autocomplete="off" class="campo-formulario mb-2" placeholder="Buscar projeto pelo nome" data-project-search>
                                <select id="project-id" name="project_id" class="campo-formulario @error('project_id') campo-invalido @enderror">
                                    <option value="">Selecione um projeto</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" @selected((string) old('project_id') === (string) $project->id)>{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-blue-700 hover:text-blue-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600" data-show-new-project>
                                    <span aria-hidden="true">+</span> Cadastrar novo projeto
                                </button>
                            </div>
                            <div id="new-project-fields" class="hidden rounded-lg border border-blue-200 bg-blue-50/60 p-3">
                                <label for="new-project-name" class="mb-1.5 block text-sm font-semibold text-slate-800">Nome do novo projeto</label>
                                <input id="new-project-name" name="new_project_name" type="text" maxlength="255" class="campo-formulario @error('new_project_name') campo-invalido @enderror" value="{{ old('new_project_name') }}" placeholder="Ex.: Ferramentas Internas" disabled>
                                <button type="button" class="mt-2 text-sm font-semibold text-blue-700 hover:text-blue-900" data-show-existing-project>Selecionar projeto existente</button>
                            </div>
                            @error('project_id') <p class="mensagem-erro">{{ $message }}</p> @enderror
                            @error('new_project_name') <p class="mensagem-erro">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grupo-campo">
                        <label for="project-task" class="rotulo-campo">Tarefa do projeto</label>
                        <div class="conteudo-campo">
                            <input id="project-task" name="project_task" type="text" list="recent-project-tasks" maxlength="255" class="campo-formulario @error('project_task') campo-invalido @enderror" value="{{ old('project_task') }}" placeholder="Atividade realizada">
                            <datalist id="recent-project-tasks">@foreach ($recentTasks as $recentTask)<option value="{{ $recentTask }}">@endforeach</datalist>
                            @error('project_task') <p class="mensagem-erro">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grupo-campo">
                        <label for="occurrence" class="rotulo-campo">Ocorrência</label>
                        <div class="conteudo-campo">
                            <input id="occurrence" name="occurrence" type="text" maxlength="255" class="campo-formulario @error('occurrence') campo-invalido @enderror" value="{{ old('occurrence') }}" placeholder="Resumo do que foi realizado">
                            @error('occurrence') <p class="mensagem-erro">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grupo-campo items-start">
                        <label for="internal-description" class="rotulo-campo sm:pt-2.5">Descrição interna</label>
                        <div class="conteudo-campo">
                            <textarea id="internal-description" name="internal_description" rows="4" maxlength="5000" class="campo-formulario resize-y @error('internal_description') campo-invalido @enderror" placeholder="Detalhes adicionais para consulta interna">{{ old('internal_description') }}</textarea>
                            @error('internal_description') <p class="mensagem-erro">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grupo-campo">
                        <label for="entry-type" class="rotulo-campo">Tipo <span aria-hidden="true">*</span></label>
                        <div class="conteudo-campo">
                            <select id="entry-type" name="entry_type" class="campo-formulario @error('entry_type') campo-invalido @enderror" required>
                                <option value="work" @selected(old('entry_type', 'work') === 'work')>Trabalho</option>
                                <option value="overtime" @selected(old('entry_type') === 'overtime')>Hora extra</option>
                            </select>
                            @error('entry_type') <p class="mensagem-erro">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grupo-campo">
                        <label for="owner" class="rotulo-campo">Responsável</label>
                        <div class="conteudo-campo">
                            <input id="owner" name="owner" type="text" maxlength="255" autocomplete="name" class="campo-formulario @error('owner') campo-invalido @enderror" value="{{ old('owner') }}" placeholder="Nome da pessoa">
                            @error('owner') <p class="mensagem-erro">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="sticky bottom-0 flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <button type="button" class="botao-secundario justify-center" data-close-modal>Cancelar</button>
                    <button id="submit-button" type="submit" class="botao-primario justify-center">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.agendaData = {
            dataSelecionada: {{ Js::from($selectedDate->toDateString()) }},
            apontamentos: {{ Js::from($appointments->mapWithKeys(fn ($appointment) => [
                $appointment->id => [
                    'id' => $appointment->id,
                    'date' => $appointment->date->toDateString(),
                    'duration' => $appointment->formattedDuration(),
                    'project_id' => $appointment->project_id,
                    'project_task' => $appointment->project_task,
                    'occurrence' => $appointment->occurrence,
                    'internal_description' => $appointment->internal_description,
                    'entry_type' => $appointment->entry_type,
                    'owner' => $appointment->owner,
                ],
            ])) }},
            validacao: {
                possuiErros: {{ Js::from($errors->any()) }},
                valores: {{ Js::from([
                    'id' => old('appointment_id'),
                    'date' => old('date', $selectedDate->toDateString()),
                    'duration' => old('duration'),
                    'project_id' => old('project_id'),
                    'new_project_name' => old('new_project_name'),
                    'project_task' => old('project_task'),
                    'occurrence' => old('occurrence'),
                    'internal_description' => old('internal_description'),
                    'entry_type' => old('entry_type', 'work'),
                    'owner' => old('owner'),
                ]) }},
            },
        };
    </script>
</body>
</html>
