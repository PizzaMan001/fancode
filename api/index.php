<?php
/**
 * Fancode Live Events - Vercel Optimized Edition
 * Responsive + Dark/Light Mode + Full Time/Date
 */

// Fetch JSON Data
$json_url = "https://raw.githubusercontent.com/drmlive/fancode-live-events/refs/heads/main/fancode.json";
$matches = [];

$res = @file_get_contents($json_url);
if ($res) {
    $data = json_decode($res, true);
    $matches = $data['matches'] ?? [];
}

// Stats & Categories
$current_cat = $_GET['cat'] ?? 'all';
$categories = array_unique(array_column($matches, 'event_category'));
sort($categories);

$display_matches = ($current_cat === 'all') 
    ? $matches 
    : array_filter($matches, fn($m) => ($m['event_category'] ?? '') === $current_cat);

$total_count = count($matches);
$live_count = count(array_filter($matches, fn($m) => strtoupper($m['status'] ?? '') === 'LIVE'));
$upcoming_count = $total_count - $live_count;

$icons = [
    'Cricket' => 'fa-bat-ball', 'Football' => 'fa-futbol',
    'Tennis' => 'fa-table-tennis-paddle-ball', 'Golf' => 'fa-golf-ball-tee',
    'MotoGP' => 'fa-motorcycle', 'Formula 1' => 'fa-car-side'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fancode Live - Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .live-pulse { animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-300">

    <header class="bg-[#2d3291] text-white pt-10 pb-20 px-4 rounded-b-[3rem] shadow-2xl relative">
        <div class="container mx-auto text-center">
            <h1 class="text-3xl font-black uppercase mb-4">Fancode Live Events</h1>
            <div class="inline-flex items-center bg-black/20 backdrop-blur-md px-6 py-2 rounded-full border border-white/10 text-[10px] font-bold tracking-widest gap-4">
                <span class="text-red-400"><i class="fas fa-broadcast-tower mr-1.5"></i> <?php echo $live_count; ?> LIVE</span>
                <span class="text-yellow-400"><i class="far fa-clock mr-1.5"></i> <?php echo $upcoming_count; ?> UPCOMING</span>
                <span><i class="fas fa-layer-group mr-1.5 text-blue-300"></i> <?php echo $total_count; ?> TOTAL</span>
            </div>
            <div id="clock" class="mt-6 text-[11px] font-bold tracking-widest opacity-70"></div>
        </div>
        <button onclick="toggleTheme()" class="absolute top-6 right-6 w-10 h-10 bg-white/10 rounded-full flex items-center justify-center border border-white/20">
            <i id="theme-icon" class="fas fa-moon"></i>
        </button>
    </header>

    <main class="container mx-auto px-4 -mt-10">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl p-6 mb-10 border border-slate-100 dark:border-slate-800">
            <div class="flex gap-3 overflow-x-auto no-scrollbar">
                <a href="?cat=all" class="flex-none px-6 py-2.5 rounded-2xl text-[11px] font-black <?php echo $current_cat == 'all' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'; ?>">ALL EVENTS</a>
                <?php foreach($categories as $cat): if(empty($cat)) continue; ?>
                    <a href="?cat=<?php echo urlencode($cat); ?>" class="flex-none px-6 py-2.5 rounded-2xl text-[11px] font-black <?php echo $current_cat == $cat ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'; ?>">
                        <i class="fas <?php echo $icons[$cat] ?? 'fa-tag'; ?> mr-2"></i><?php echo strtoupper($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-20">
            <?php foreach($display_matches as $match): 
                $status = strtoupper($match['status'] ?? 'UPCOMING');
                $is_live = ($status === 'LIVE');
                $watch_url = $match['adfree_url'] ?? $match['dai_url'] ?? '#';
            ?>
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col group transition-all hover:shadow-2xl">
                <div class="relative aspect-video">
                    <img src="<?php echo $match['src'] ?? ''; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-duration-700">
                    <div class="absolute top-4 right-4">
                        <?php if($is_live): ?>
                            <span class="bg-red-600 text-white text-[9px] font-black px-3 py-1 rounded-xl shadow-lg flex items-center"><span class="w-1.5 h-1.5 bg-white rounded-full mr-2 live-pulse"></span> LIVE</span>
                        <?php else: ?>
                            <span class="bg-yellow-500 text-white text-[9px] font-black px-3 py-1 rounded-xl shadow-lg">UPCOMING</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="p-6 flex-grow">
                    <h3 class="font-bold text-sm leading-tight mb-4 h-10 line-clamp-2"><?php echo htmlspecialchars($match['title'] ?? ''); ?></h3>
                    <div class="flex items-center text-[9px] text-blue-600 font-black mb-2 uppercase">
                        <i class="fas <?php echo $icons[$match['event_category'] ?? ''] ?? 'fa-trophy'; ?> mr-2"></i>
                        <?php echo $match['event_category'] ?? 'Sports'; ?>
                    </div>
                    <div class="text-[9px] text-slate-400 font-bold uppercase"><i class="far fa-calendar-alt mr-2"></i><?php echo $match['startTime'] ?? ''; ?></div>
                </div>

                <div class="px-6 pb-6 mt-auto">
                    <?php if($is_live): ?>
                        <a href="<?php echo htmlspecialchars($watch_url); ?>" target="_blank" class="block w-full text-center py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-black uppercase rounded-2xl transition-all">Watch Live</a>
                    <?php else: ?>
                        <div class="w-full text-center py-3.5 bg-slate-50 dark:bg-slate-800/50 text-slate-400 text-[10px] font-black uppercase rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">Available Soon</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        function updateTime() {
            const now = new Date();
            let hours = now.getHours();
            let mins = now.getMinutes();
            let secs = now.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            const strTime = `${hours}:${String(mins).padStart(2,'0')}:${String(secs).padStart(2,'0')} ${ampm} ${String(now.getDate()).padStart(2,'0')}-${String(now.getMonth()+1).padStart(2,'0')}-${now.getFullYear()}`;
            document.getElementById('clock').innerText = strTime;
        }
        setInterval(updateTime, 1000); updateTime();

        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            document.getElementById('theme-icon').className = isDark ? 'fas fa-sun' : 'fas fa-moon';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
        if(localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
            document.getElementById('theme-icon').className = 'fas fa-sun';
        }
    </script>
</body>
</html>
