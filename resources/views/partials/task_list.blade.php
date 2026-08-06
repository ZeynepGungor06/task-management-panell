@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@foreach($tasks as $task)
<!-- ANA GÖREV KARTI -->
<div class="task-item {{ $task->is_completed ? 'completed' : '' }}">
    <div class="task-title">
        <span class="fw-bold">{{ $task->title }}</span>
        
        <div class="mt-1">
            @foreach($task->tags as $tag)
            <span class="badge" style="background-color: {{ $tag->color }}; color: #fff; font-size: 0.75rem;"> 
                {{ $tag->name }}
            </span>
            @endforeach    
        </div>
        
        <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }} ms-2" style="font-size: 0.7rem;">
            {{ ucfirst($task->priority) }}
        </span>
        @if($task->due_date)
            <small class="ms-2 text-{{ $task->due_date->isPast() ? 'danger fw-bold' : 'muted' }}" style="font-size: 0.8rem;">
                <i class="bi bi-calendar"></i> {{ $task->due_date->format('d.m.Y H:i') }}
            </small>
        @endif
    </div>
    
    <div class="task-actions">
        @if($task->due_date && $task->due_date->isPast())
            <span class="text-danger small fw-bold me-3"><i class="bi bi-clock-history"></i> Süre Doldu</span>
        @else
            <form action="{{ route('tasks.update', $task->id) }}" method="POST" style="margin:0;">
                @csrf
                @method('PUT')
                @if($task->is_completed)
                    <button type="submit" class="btn btn-completed">Completed</button>
                @else
                    <button type="submit" class="btn btn-pending">Pending</button>
                @endif
            </form>
        @endif
        
        <!-- YENİ: ALT GÖREVLER BUTONU -->
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#subtasksModal{{ $task->id }}">
            <i class="bi bi-diagram-3"></i> Alt Görevler
            @if($task->children->count() > 0)
                <span class="badge bg-light text-dark ms-1">{{ $task->children->count() }}</span>
            @endif
        </button>

        <!-- Detay Butonu -->
        <button type="button" class="btn" style="background-color: #5b8fb9; color: white;" data-bs-toggle="modal" data-bs-target="#taskDetailModal{{ $task->id }}">
            Detay
        </button>
        
        @if(auth()->user()->role !== 'user')
            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">Sil</button>
            </form>
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- 1. YENİ: ALT GÖREVLER LİSTESİ MODALI       -->
<!-- ========================================== -->
<div class="modal fade" id="subtasksModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"> 
        <div class="modal-content bg-light"> 
            
            <div class="modal-header bg-white">
                <h5 class="modal-title text-primary"><i class="bi bi-diagram-3"></i> {{ $task->title }} - Alt Görevleri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            
            <div class="modal-body p-4">
                @if($task->children->isNotEmpty())
                    @foreach($task->children as $subtask)
                        
                        <!-- ALT GÖREV KARTI -->
                        <div class="task-item {{ $subtask->is_completed ? 'completed' : '' }} bg-white shadow-sm mb-3" style="border: 1px solid #dee2e6; border-radius: 8px;">
                            <div class="task-title">
                                <span class="text-muted me-2">↳</span><span class="fw-bold">{{ $subtask->title }}</span>
                                
                                <div class="mt-1">
                                    @foreach($subtask->tags as $tag)
                                    <span class="badge" style="background-color: {{ $tag->color }}; color: #fff; font-size: 0.75rem;"> 
                                        {{ $tag->name }}
                                    </span>
                                    @endforeach    
                                </div>
                                
                                <span class="badge bg-{{ $subtask->priority == 'high' ? 'danger' : ($subtask->priority == 'medium' ? 'warning' : 'info') }} ms-2" style="font-size: 0.7rem;">
                                    {{ ucfirst($subtask->priority) }}
                                </span>
                            </div>
                            
                            <div class="task-actions">
                                <form action="{{ route('tasks.update', $subtask->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('PUT')
                                    @if($subtask->is_completed)
                                        <button type="submit" class="btn btn-completed">Completed</button>
                                    @else
                                        <button type="submit" class="btn btn-pending">Pending</button>
                                    @endif
                                </form>
                                
                                <!-- DÜZELTİLDİ: Sadece data-bs-toggle kullanıyoruz, modalı direkt değiştirir -->
                                <button type="button" class="btn" style="background-color: #5b8fb9; color: white;" data-bs-toggle="modal" data-bs-target="#taskDetailModal{{ $subtask->id }}">
                                    Detay
                                </button>
                                
                                @if(auth()->user()->role !== 'user')
                                    <form action="{{ route('tasks.destroy', $subtask->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete">Sil</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    @endforeach
                @else
                    <div class="alert alert-info border text-center text-muted">
                        Bu göreve ait alt görev bulunmamaktadır. Sağ taraftaki formdan ekleyebilirsiniz.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- =============================================================== -->
<!-- 2. ALT GÖREVLERİN KENDİ DETAY MODALLARI (DOM'UN DIŞINA ÇIKTI) -->
<!-- =============================================================== -->
@foreach($task->children as $subtask)
<div class="modal fade" id="taskDetailModal{{ $subtask->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <!-- DÜZELTİLDİ: Listeye Geri Dön Butonu -->
                <button class="btn btn-sm btn-outline-secondary me-3" data-bs-toggle="modal" data-bs-target="#subtasksModal{{ $task->id }}">
                    <i class="bi bi-arrow-left"></i> Geri
                </button>
                <h5 class="modal-title text-secondary">Alt Görev Detayı: {{ $subtask->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                
                <div class="d-flex justify-content-between align-items-center bg-white border p-3 rounded mb-4 shadow-sm">
                    <div>
                        <small class="text-muted d-block mb-1">Bağlı Olduğu Ana Görev</small>
                        <span class="fw-bold text-primary">{{ $task->title }}</span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block mb-1">Önem Derecesi</small>
                        <span class="badge bg-{{ $subtask->priority == 'high' ? 'danger' : ($subtask->priority == 'medium' ? 'warning' : 'info') }}">
                            {{ ucfirst($subtask->priority) }}
                        </span>
                    </div>
                </div>

                <!-- ALT GÖREV DOSYALAR BÖLÜMÜ -->
                <h6 class="border-bottom pb-2 mb-3">Eklenen Dosyalar (Alt Görev)</h6>
                <form action="{{ route('files.store', $subtask->id) }}" method="POST" enctype="multipart/form-data" class="mb-3 d-flex gap-2">
                    @csrf
                    <input class="form-control form-control-sm" type="file" name="file" required>
                    <button class="btn btn-sm btn-primary" type="submit">Yükle</button>
                </form>
                
                <div class="row g-2 mb-4">
                    @forelse($subtask->files as $file)
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                    <div class="text-truncate me-2">
                                        <i class="bi bi-file-earmark-text me-1 text-primary"></i>
                                        <small title="{{ $file->original_name }}">{{ \Illuminate\Support\Str::limit($file->original_name, 20) }}</small>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('files.download', $file->id) }}" class="btn btn-sm btn-outline-success p-1"><i class="bi bi-download"></i></a>
                                        <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger p-1"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small">Bu alt göreve henüz dosya eklenmemiş.</p>
                    @endforelse
                </div>

                <!-- ALT GÖREV YORUMLAR BÖLÜMÜ -->
                <h6 class="border-bottom pb-2 mb-3 mt-4">Yorumlar (Alt Görev)</h6>
                <form action="{{ route('comments.store', $subtask->id) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="form-group mb-2">
                        <textarea name="comment" class="form-control" rows="2" placeholder="Yorum yaz..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-sm btn-primary">Gönder</button>
                    </div>
                </form>
                
                <div class="list-group">
                    @forelse($subtask->comments as $comment)
                        <div class="list-group-item list-group-item-action {{ $comment->is_spam ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1 fw-bold {{ $comment->is_spam ? 'text-secondary' : 'text-primary' }}">{{ $comment->user->name }}</h6>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-sm mt-2">{{ $comment->comment }}</p>
                            <div class="d-flex justify-content-end gap-3 mt-1">
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager' || auth()->id() === $comment->user_id)
                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none" style="font-size: 0.8rem;">Sil</button>
                                </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small">Henüz yorum yapılmamış.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach


<!-- ========================================== -->
<!-- 3. ANA GÖREV DETAY MODALI                  -->
<!-- ========================================== -->
<div class="modal fade" id="taskDetailModal{{ $task->id }}" tabindex="-1" aria-labelledby="taskDetailModalLabel{{ $task->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailModalLabel{{ $task->id }}">{{ $task->title }} - Detaylar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            
            <div class="modal-body">
                
                @if($task->due_date && $task->due_date->isPast())
                    <div class="alert alert-danger p-2 text-center small mb-3">
                        <i class="bi bi-clock-history"></i> Bu görevin süresi ({{ $task->due_date->format('d.m.Y H:i') }}) dolmuştur. Dosya veya yorum eklenemez.
                    </div>
                @endif

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                    <form action="{{ route('tasks.update_details', $task->id) }}" method="POST" class="mb-4 p-3 bg-light border rounded">
                        @csrf
                        @method('PATCH')
                        <h6 class="mb-2 text-secondary" style="font-size: 0.9rem;">Görevi Düzenle (Sadece Yönetici)</h6>
                        <div class="d-flex gap-2">
                            <select name="priority" class="form-control form-control-sm">
                                <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Düşük</option>
                                <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Orta</option>
                                <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>Yüksek</option>
                            </select>
                            <input type="datetime-local" name="due_date" class="form-control form-control-sm" value="{{ $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '' }}">
                            <button type="submit" class="btn btn-sm btn-warning">Güncelle</button>
                        </div>
                    </form>
                @endif

                <div class="d-flex justify-content-between align-items-center bg-white border p-3 rounded mb-4 shadow-sm">
                    <div>
                        <small class="text-muted d-block mb-1">Önem Derecesi</small>
                        <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block mb-1">Son Teslim Tarihi</small>
                        @if($task->due_date)
                            <span class="{{ $task->due_date->isPast() ? 'text-danger fw-bold' : 'text-dark' }}">
                                <i class="bi bi-calendar"></i> {{ $task->due_date->format('d.m.Y H:i') }}
                            </span>
                            @if($task->due_date->isPast())
                                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> Süre Doldu</div>
                            @endif
                        @else
                            <span class="text-muted fst-italic">Tarih belirtilmedi</span>
                        @endif
                    </div>
                </div>

                <!-- DOSYALAR BÖLÜMÜ -->
                <h6 class="border-bottom pb-2 mb-3">Eklenen Dosyalar</h6>
                
                @if(!($task->due_date && $task->due_date->isPast()))
                    <form action="{{ route('files.store', $task->id) }}" method="POST" enctype="multipart/form-data" class="mb-3 d-flex gap-2">
                        @csrf
                        <input class="form-control form-control-sm" type="file" name="file" required>
                        <button class="btn btn-sm btn-primary" type="submit">Yükle</button>
                    </form>
                @endif

                <div class="row g-2 mb-4">
                    @forelse($task->files as $file)
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                <div class="text-truncate me-2">
                                    <i class="bi bi-file-earmark-text me-1 text-primary"></i>
                                    <small title="{{ $file->original_name }}">{{ \Illuminate\Support\Str::limit($file->original_name, 20) }}</small>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-sm btn-outline-success p-1">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="d-inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger p-1"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small">Henüz dosya eklenmemiş.</p>
                    @endforelse
                </div>

                <!-- YORUMLAR BÖLÜMÜ -->
                <h6 class="border-bottom pb-2 mb-3 mt-4">Yorumlar</h6>
                
                @if(!($task->due_date && $task->due_date->isPast()))
                    <form action="{{ route('comments.store', $task->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="form-group mb-2">
                            <textarea name="comment" class="form-control" rows="2" placeholder="Görevle ilgili bir yorum yaz..." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-sm btn-primary">Gönder</button>
                        </div>
                    </form>
                @endif

                <div class="list-group">
                    @forelse($task->comments as $comment)
                    <div class="list-group-item list-group-item-action {{ $comment->is_spam ? 'bg-light' : '' }}">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h6 class="mb-1 fw-bold {{ $comment->is_spam ? 'text-secondary' : 'text-primary' }}">{{ $comment->user->name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        
                        @if($comment->is_spam)
                            <p class="mb-1 text-sm mt-2 fst-italic text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Bu yorum admin tarafından spam olarak işaretlenmiştir.
                            </p>
                        @else
                            <p class="mb-1 text-sm mt-2">{{ $comment->comment }}</p>
                        @endif
                        
                        <div class="d-flex justify-content-end gap-3 mt-1">
                            
                            @if(auth()->user()->role === 'admin')
                            <form action="{{ route('comments.spam', $comment->id) }}" method="POST" class="m-0">
                                @csrf 
                                @method('PUT')
                                <button type="submit" class="btn btn-link p-0 text-decoration-none {{ $comment->is_spam ? 'text-success' : 'text-warning' }}" style="font-size: 0.8rem;">
                                    {{ $comment->is_spam ? 'Spam İşaretini Kaldır' : 'Spamla' }}
                                </button>
                            </form>
                            @endif

                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager' || auth()->id() === $comment->user_id)
                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="m-0">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none" style="font-size: 0.8rem;">Sil</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small">Henüz yorum yapılmamış.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>
@endforeach