@foreach($tasks as $task)
<div class="task-item {{ $task->is_completed ? 'completed' : '' }}">
    <div class="task-title">
        {{ $task->title }}
        
        <!-- YENİ: Görevin yanında önem derecesini ve tarihini gösteriyoruz -->
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
        
        <!-- YENİ: Süre dolduysa Pending/Completed butonunu gizleyip uyarı basıyoruz -->
        @if($task->due_date && $task->due_date->isPast())
            <span class="text-danger small fw-bold me-3"><i class="bi bi-clock-history"></i> Süre Doldu</span>
        @else
            <!-- Completed / Pending Değiştirme Formu -->
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
        
        <!-- Detay Butonu -->
        <button type="button" class="btn" style="background-color: #5b8fb9;" data-bs-toggle="modal" data-bs-target="#taskDetailModal{{ $task->id }}">
            Detay
        </button>
        
        <!-- Silme Formu (Normal User Silemez) -->
        @if(auth()->user()->role !== 'user')
            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">Sil</button>
            </form>
        @endif
    </div>
</div>

<!-- Görev Detay Modalı -->
<div class="modal fade" id="taskDetailModal{{ $task->id }}" tabindex="-1" aria-labelledby="taskDetailModalLabel{{ $task->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailModalLabel{{ $task->id }}">{{ $task->title }} - Detaylar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            
            <div class="modal-body">
                
                <!-- YENİ: Süre dolduysa Modalin en üstünde genel bir uyarı gösteriyoruz -->
                @if($task->due_date && $task->due_date->isPast())
                    <div class="alert alert-danger p-2 text-center small mb-3">
                        <i class="bi bi-clock-history"></i> Bu görevin süresi ({{ $task->due_date->format('d.m.Y H:i') }}) dolmuştur. Dosya veya yorum eklenemez.
                    </div>
                @endif

                <!-- YENİ: Admin ve Müdürler için Görev Güncelleme Formu (Modalin en üstüne koyduk) -->
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
                <!-- YENİ: HERKESİN GÖREBİLECEĞİ GÖREV BİLGİ PANELİ -->
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
                
                <!-- YENİ: Dosya Yükleme Formunu sadece süre geçmediyse gösteriyoruz -->
                @if(!($task->due_date && $task->due_date->isPast()))
                    <form action="{{ route('files.store', $task->id) }}" method="POST" enctype="multipart/form-data" class="mb-3 d-flex gap-2">
                        @csrf
                        <input class="form-control form-control-sm" type="file" name="file" required>
                        <button class="btn btn-sm btn-primary" type="submit">Yükle</button>
                    </form>
                @endif

                <!-- Yüklü Dosyaların Listesi -->
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
                                    <!-- İndirme Butonu -->
                                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-sm btn-outline-success p-1">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <!-- Silme Butonu -->
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
                
                <!-- YENİ: Yorum Ekleme Formunu sadece süre geçmediyse gösteriyoruz -->
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

                <!-- Yorumlar Listesi (SPAM ÖZELLİĞİ EKLENMİŞ HALİ) -->
                <div class="list-group">
                    @forelse($task->comments as $comment)
                    <div class="list-group-item list-group-item-action {{ $comment->is_spam ? 'bg-light' : '' }}">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h6 class="mb-1 fw-bold {{ $comment->is_spam ? 'text-secondary' : 'text-primary' }}">{{ $comment->user->name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        
                        <!-- Yorum İçeriği (Spam Kontrolü) -->
                        @if($comment->is_spam)
                            <p class="mb-1 text-sm mt-2 fst-italic text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Bu yorum admin tarafından spam olarak işaretlenmiştir.
                            </p>
                        @else
                            <p class="mb-1 text-sm mt-2">{{ $comment->comment }}</p>
                        @endif
                        
                        <!-- Butonlar (Spam ve Sil) -->
                        <div class="d-flex justify-content-end gap-3 mt-1">
                            
                            <!-- Sadece Admin Spamlayabilir -->
                            @if(auth()->user()->role === 'admin')
                            <form action="{{ route('comments.spam', $comment->id) }}" method="POST" class="m-0">
                                @csrf 
                                @method('PUT')
                                <button type="submit" class="btn btn-link p-0 text-decoration-none {{ $comment->is_spam ? 'text-success' : 'text-warning' }}" style="font-size: 0.8rem;">
                                    {{ $comment->is_spam ? 'Spam İşaretini Kaldır' : 'Spamla' }}
                                </button>
                            </form>
                            @endif

                            <!-- Sadece yorumu yazan kişi veya admin/müdür silebilir -->
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