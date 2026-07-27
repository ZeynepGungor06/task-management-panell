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
        
        <div style="display: flex; align-items: center; gap: 20px;">
            
            <!-- BİLDİRİM ZİLİ (BOOTSTRAP DROPDOWN) -->
            <div class="dropdown">
                <button class="btn btn-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: transparent;">
                    <i class="bi bi-bell-fill" style="font-size: 1.2rem; color: #5b8fb9;"></i>
                    <!-- Okunmamış Bildirim Sayısı -->
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                    <li><h6 class="dropdown-header fw-bold">Bildirimler</h6></li>
                    
                    @forelse(auth()->user()->unreadNotifications as $notification)
                        <li class="border-bottom px-3 py-2" style="background-color: #f8fbff;">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-dark">{{ $notification->data['message'] }}</small>
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm text-success p-0 ms-2" title="Okundu İşaretle">
                                        <i class="bi bi-check2-all"></i>
                                    </button>
                                </form>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</small>
                        </li>
                    @empty
                        <li><span class="dropdown-item text-muted text-center" style="font-size: 0.9rem;">Yeni bildiriminiz yok.</span></li>
                    @endforelse
                </ul>
            </div>

            <!-- ÇIKIŞ YAP BUTONU -->
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">Çıkış Yap</button>
            </form>
        </div>
    </div>

    <div class="container">
       
        
        <!-- SOL TARAF: BÜYÜK KUTU (Görev Listesi) -->
        <div class="left-panel">
            @if(session('breadcrumb_error'))
                <div style="background-color: #f8d7da; color: #842029; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f5c2c7; font-size: 14px; display: flex; align-items: center;">
                    <i class="bi bi-exclamation-circle-fill me-2" style="margin-right: 8px;"></i>
                    <span><strong>Hata:</strong> {{ session('breadcrumb_error') }}</span>
                </div>
            @endif
            <h2>
                @if(($user->role === 'admin' || $user->role === 'manager') && isset($selectedUser) && $selectedUser->id !== $user->id)
                    {{ $selectedUser->name }} Kullanıcısının Görevleri
                @else
                    Görevlerim
                @endif
            </h2>

            @if(count($tasks) > 0)
               <div id="task-container">
                @include('partials.task_list')

               </div>
               <div id="loading-spinner" style="display: none; text-align: center; padding: 20px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
               </div>
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
                <div class="form-group ">
                    <label>Önem Derecesi</label>
                    <select name="priority" required>
                        <option value="low">Düşük Önem</option>
                        <option value="medium">Orta Önem</option>
                        <option value="high">Yüksek Önem</option>
                    </select>
                </div>
                <div class="form-group">
                    <label >Son Teslim Tarihi</label>
                    <input type="datetime-local" name="due_date" class="form-control">
                </div>

             <button type="submit" class="btn-submit">Ekle</button>
            </form>
            
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let page=1;
    let isLoading=false;
    let hasMoreData=true;
    window.addEventListener('scroll',() => {
        if(window.innerHeight + window.scrollY >= document.body.offsetHeight - 100){
            if(!isLoading && hasMoreData){
                loadMoreTasks();
            }
        }
    });
    function loadMoreTasks(){
        isLoading=true;
        page++;

        document.getElementById('loading-spinner').style.display='block';
        let url=new URL(window.location.href);
        url.searchParams.set('page',page);

        fetch(url,{
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading-spinner').style.display='none';
            if(data.html.trim()===''){
                hasMoreData=false;
                return;
            }

            document.getElementById('task-container').insertAdjacentHTML('beforeend',data.html);
            isLoading=false;

        })
        .catch(error => {
            console.error('Sunucu Hatası:', error);
            isLoading=false;
        });
    }
</script>
</body>
</html>