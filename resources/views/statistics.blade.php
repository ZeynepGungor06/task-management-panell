<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İstatikler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #eef5fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background-color: #ffffff; padding: 15px 30px; display: flex; justify-content: space-between; box-shadow: 0 4px 12px rgba(114, 174, 230, 0.1); }
        .navbar h1 { margin: 0; color: #5b8fb9; font-size: 20px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #5b8fb9; }
        .stat-icon { font-size: 2rem; color: #5b8fb9; margin-bottom: 10px; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #333; margin: 0; }
        .stat-title { color: #6b8299; font-size: 14px; font-weight: 600; text-transform: uppercase; }
        .table-container { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-top: 30px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>To-Do List <span style="font-size: 14px; color: #6b8299;">({{ $user->name }} - İstatistik Paneli)</span></h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary fw-bold">
            <i class="bi bi-arrow-left"></i> Görevlere Dön
        </a>
    </div>
    <div class="container mt-5">
        <!-- ÜST KARTLAR -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card" style="border-bottom-color: #0dcaf0;">
                    <i class="bi bi-card-list stat-icon" style="color: #0dcaf0;"></i>
                    <p class="stat-number">{{ $totalTasks }}</p>
                    <p class="stat-title">Toplam Görev</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-bottom-color: #198754;">
                    <i class="bi bi-check-circle-fill stat-icon" style="color: #198754;"></i>
                    <p class="stat-number">{{ $completedTasks }}</p>
                    <p class="stat-title">Tamamlanan</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-bottom-color: #ffc107;">
                    <i class="bi bi-hourglass-split stat-icon" style="color: #ffc107;"></i>
                    <p class="stat-number">{{ $pendingTasks }}</p>
                    <p class="stat-title">Bekleyen</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-bottom-color: #dc3545;">
                    <i class="bi bi-exclamation-triangle-fill stat-icon" style="color: #dc3545;"></i>
                    <p class="stat-number">{{ $overdueTasks }}</p>
                    <p class="stat-title">Süresi Geçen</p>
                </div>
            </div>
        </div>
    </div>
    <!-- PERSONEL PERFORMANS TABLOSU -->
        <div class="table-container">
            <h4 class="mb-4" style="color: #5b8fb9;"><i class="bi bi-people-fill"></i> Personel Performans Raporu</h4>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Personel Adı</th>
                        <th>Rolü</th>
                        <th class="text-center">Toplam Görev</th>
                        <th class="text-center text-success">Tamamlanan</th>
                        <th class="text-center text-warning">Bekleyen</th>
                        <th>Başarı Oranı</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usersStats as $staff)
                        @php
                            $successRate = $staff->tasks_count > 0 
                                ? round(($staff->completed_tasks_count / $staff->tasks_count) * 100) 
                                : 0;
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $staff->name }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($staff->role) }}</span></td>
                            <td class="text-center fw-bold">{{ $staff->tasks_count }}</td>
                            <td class="text-center text-success fw-bold">{{ $staff->completed_tasks_count }}</td>
                            <td class="text-center text-warning fw-bold">{{ $staff->pending_tasks_count }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $successRate == 100 ? 'bg-success' : 'bg-primary' }}" 
                                         role="progressbar" 
                                         style="width: {{ $successRate }}%;" 
                                         aria-valuenow="{{ $successRate }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        %{{ $successRate }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>