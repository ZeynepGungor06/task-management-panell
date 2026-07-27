 @foreach($tasks as $task)
                    <div class="task-item {{ $task->is_completed ? 'completed' : '' }}">
                        <div class="task-title">{{ $task->title }}</div>
                        <div class="task-actions">
                            
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
                            
                            <!-- DOSYALAR BÖLÜMÜ -->
                            <h6 class="border-bottom pb-2 mb-3">Eklenen Dosyalar</h6>
                            
                            <!-- Dosya Yükleme Formu -->
                            <form action="{{ route('files.store', $task->id) }}" method="POST" enctype="multipart/form-data" class="mb-3 d-flex gap-2">
                                @csrf
                                <input class="form-control form-control-sm" type="file" name="file" required>
                                <button class="btn btn-sm btn-primary" type="submit">Yükle</button>
                            </form>

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
                            
                            <!-- Yorum Ekleme Formu -->
                            <form action="{{ route('comments.store', $task->id) }}" method="POST" class="mb-3">
                                @csrf
                                <div class="form-group mb-2">
                                    <textarea name="comment" class="form-control" rows="2" placeholder="Görevle ilgili bir yorum yaz..." required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-sm btn-primary">Gönder</button>
                                </div>
                            </form>

                            <!-- Yorumlar Listesi -->
                            <div class="list-group">
                                @forelse($task->comments as $comment)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 fw-bold text-primary">{{ $comment->user->name }}</h6>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-sm mt-2">{{ $comment->comment }}</p>
                                    
                                    <!-- Sadece yorumu yazan kişi veya admin/müdür silebilir -->
                                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager' || auth()->id() === $comment->user_id)
                                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="text-end mt-1">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none" style="font-size: 0.8rem;">Sil</button>
                                    </form>
                                    @endif
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