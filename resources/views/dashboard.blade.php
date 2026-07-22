<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #eef5fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            color: #333;
        }
        .navbar {
            background-color: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(114, 174, 230, 0.1);
        }
        .navbar h1 {
            margin: 0;
            color: #5b8fb9;
            font-size: 20px;
        }
        .logout-btn {
            background-color: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .logout-btn:hover { background-color: #e53935; }
        
        .container {
            display: flex;
            max-width: 1100px;
            margin: 40px auto;
            gap: 24px;
            padding: 0 20px;
        }
        
        .left-panel {
            flex: 7;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(114, 174, 230, 0.15);
        }
        
        .right-panel {
            flex: 3;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(114, 174, 230, 0.15);
            height: fit-content;
        }
        
        h2 {
            color: #5b8fb9;
            margin-top: 0;
            border-bottom: 2px solid #eef5fa;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px;
            border: 1px solid #d1e3f2;
            border-radius: 8px;
            margin-bottom: 12px;
            background-color: #f7fbff;
            transition: 0.3s;
        }
        .task-item:hover { border-color: #72aee6; }
        
        .task-item.completed {
            background-color: #f0f7f2;
            border-color: #c8e6c9;
        }
        .task-item.completed .task-title {
            text-decoration: line-through;
            color: #8c9eae;
        }
        
        .task-title { font-size: 16px; font-weight: 500; }
        .task-actions { display: flex; gap: 10px; }
        
        .btn {
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            color: white;
            font-size: 13px;
        }
        .btn-pending { background-color: #f4b41a; color: #fff;}
        .btn-completed { background-color: #4caf50; }
        .btn-delete { background-color: #e57373; }
        
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #6b8299;
            font-size: 14px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1e3f2;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: #f7fbff;
            outline: none;
        }
        .form-group input:focus, .form-group select:focus { border-color: #72aee6; }
        
        .btn-submit {
            background-color: #72aee6;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-submit:hover { background-color: #5a94d0; }
        
        .admin-filter {
            background-color: #eef5fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .admin-filter h3 {
            margin-top: 0;
            color: #5b8fb9;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Üst Menü -->
    <div class="navbar">
        <h1>To-Do List 
            <span style="font-size: 14px; color: #6b8299; margin-left:10px;">
                ({{ $user->name }} - {{ ucfirst($user->role) }})
            </span>
        </h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">Çıkış Yap</button>
        </form>
    </div>

    <div class="container">
        
        <!-- SOL TARAF: BÜYÜK KUTU (Görev Listesi) -->
        <div class="left-panel">
            <h2>
                @if(($user->role === 'admin' || $user->role === 'manager') && isset($selectedUser) && $selectedUser->id !== $user->id)
                    {{ $selectedUser->name }} Kullanıcısının Görevleri
                @else
                    Görevlerim
                @endif
            </h2>

            @if(count($tasks) > 0)
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
            @else
                <p style="color: #6b8299; text-align: center; margin-top: 30px;">Gösterilecek bir görev bulunamadı.</p>
            @endif
        </div>

        <!-- SAĞ TARAF: KÜÇÜK KUTU (Kontroller ve Görev Ekleme) -->
        <div class="right-panel">
            
            <!-- Admin ve Müdürlerin Göreceği Kullanıcı Seçme Filtresi -->
            @if(($user->role === 'admin' || $user->role === 'manager') && count($users) > 0)
                <div class="admin-filter">
                    <h3>
                        {{ $user->role === 'admin' ? 'Kullanıcı Ekranına Geç' : 'Elemanlarımın Ekranına Geç' }}
                    </h3>
                    <form action="{{ route('dashboard') }}" method="GET">
                        <select name="user_id" onchange="this.form.submit()" class="form-group" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #d1e3f2;">
                            <option value="{{ $user->id }}" {{ (!request()->has('user_id') || request('user_id') == $user->id) ? 'selected' : '' }}>
                                Kendi Ekranım ({{ ucfirst($user->role) }})
                            </option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif

            <!-- Görev Ekleme Formu -->
            <h2>Yeni Görev Ekle</h2>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                
                <!-- Admin ve Müdür kime görev atayacağını seçer -->
                @if($user->role === 'admin' || $user->role === 'manager')
                    <div class="form-group">
                        <label>Kime Atanacak?</label>
                        <select name="user_id">
                            <option value="{{ $user->id }}">Kendime Atansın</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="form-group">
                    <label>Görev Adı</label>
                    <input type="text" name="title" required placeholder="Görevi buraya yazın...">
                </div>
                
                <button type="submit" class="btn-submit">Ekle</button>
            </form>
            
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>