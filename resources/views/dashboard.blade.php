<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - To-Do List</title>
    <style>
        body {
            background-color: #eef5fa; /* Soft mavi arkaplan */
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
        
        /* Sol Taraf: Büyük Kutu */
        .left-panel {
            flex: 7; /* Genişlik oranı 7 */
            background-color: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(114, 174, 230, 0.15);
        }
        
        /* Sağ Taraf: Küçük Kutu */
        .right-panel {
            flex: 3; /* Genişlik oranı 3 */
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
        .task-item:hover {
            border-color: #72aee6;
        }
        
        /* Tamamlanan Görev Stili */
        .task-item.completed {
            background-color: #f0f7f2;
            border-color: #c8e6c9;
        }
        .task-item.completed .task-title {
            text-decoration: line-through;
            color: #8c9eae;
        }
        
        .task-title {
            font-size: 16px;
            font-weight: 500;
        }
        .task-actions {
            display: flex;
            gap: 10px;
        }
        
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
        
        .form-group {
            margin-bottom: 16px;
        }
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
        .form-group input:focus, .form-group select:focus {
            border-color: #72aee6;
        }
        
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
        .btn-submit:hover {
            background-color: #5a94d0;
        }
        
        /* Admin Kullanıcı Seçme Alanı Stili */
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
                @if($user->role === 'admin' && isset($selectedUser) && $selectedUser->id !== $user->id)
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
                            
                            <!-- Silme Formu -->
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete">Sil</button>
                            </form>
                            
                        </div>
                    </div>
                @endforeach
            @else
                <p style="color: #6b8299; text-align: center; margin-top: 30px;">Gösterilecek bir görev bulunamadı.</p>
            @endif
        </div>

        <!-- SAĞ TARAF: KÜÇÜK KUTU (Kontroller ve Görev Ekleme) -->
        <div class="right-panel">
            
            <!-- Sadece Adminlerin Göreceği Kullanıcı Görüntüleme Filtresi -->
            @if($user->role === 'admin')
                <div class="admin-filter">
                    <h3>Kullanıcı Ekranına Geç</h3>
                    <form action="{{ route('dashboard') }}" method="GET">
                        <select name="user_id" onchange="this.form.submit()" class="form-group" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #d1e3f2;">
                            <option value="{{ $user->id }}" {{ (!request()->has('user_id') || request('user_id') == $user->id) ? 'selected' : '' }}>
                                Kendi Ekranım (Admin)
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
                
                <!-- Eğer Admin ise, kime görev atayacağını seçer -->
                @if($user->role === 'admin')
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

</body>
</html>