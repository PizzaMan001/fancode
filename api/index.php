<?php
/**
 * Fancode Live Events - Ultimate Vercel Edition
 * Logic: ASHAOTT / CricZ
 */

$json_url = "https://raw.githubusercontent.com/drmlive/fancode-live-events/refs/heads/main/fancode.json";
$res = @file_get_contents($json_url);
$matches = ($res) ? (json_decode($res, true)['matches'] ?? []) : [];

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
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fancode Live Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .live-pulse { animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-300">

    <header class="bg-[#2d3291] text-white pt-10 pb-20 px-4 rounded-b-[3.5rem] shadow-2xl relative">
        <div class="container mx-auto text-center">
            <h1 class="text-3xl font-black uppercase mb-4 tracking-tighter">Fancode Live Events</h1>
            
            <div class="inline-flex items-center bg-black/20 backdrop-blur-md px-6 py-2.5 rounded-full border border-white/10 text-[10px] font-bold tracking-widest gap-4">
                <span class="text-red-400 flex items-center"><i class="fas fa-tower-broadcast mr-2"></i> <?php echo $live_count; ?> LIVE</span>
                <span class="text-yellow-400 flex items-center"><i class="far fa-clock mr-2"></i> <?php echo $upcoming_count; ?> UPCOMING</span>
                <span class="text-blue-300 flex items-center"><i class="fas fa-list-ul mr-2"></i> <?php echo $total_count; ?> TOTAL</span>
            </div>

            <div id="clock" class="mt-6 text-[12px] font-bold tracking-[0.2em] opacity-80 bg-white/10 inline-block px-5 py-2 rounded-2xl border border-white/5 uppercase"></div>
        </div>
        
        <button onclick="toggleTheme()" class="absolute top-6 right-6 w-12 h-12 bg-white/10 rounded-full flex items-center justify-center border border-white/20 hover:scale-110 transition shadow-xl">
            <i id="theme-icon" class="fas fa-moon text-lg"></i>
        </button>
    </header>

    <main class="container mx-auto px-4 -mt-12">
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl p-6 mb-10 border border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-4 text-blue-600 font-black text-[11px] uppercase tracking-widest">
                <i class="fas fa-filter"></i> Filter by Category
            </div>
            <div class="flex gap-3 overflow-x-auto no-scrollbar pb-1">
                <a href="?cat=all" class="flex-none px-6 py-3 rounded-2xl text-[11px] font-black transition-all <?php echo $current_cat == 'all' ? 'bg-blue-600 text-white shadow-lg' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200'; ?>">
                    <i class="fas fa-globe mr-2"></i> ALL EVENTS
                </a>
                <?php foreach($categories as $cat): if(empty($cat)) continue; ?>
                    <a href="?cat=<?php echo urlencode($cat); ?>" class="flex-none px-6 py-3 rounded-2xl text-[11px] font-black transition-all <?php echo $current_cat == $cat ? 'bg-blue-600 text-white shadow-lg' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'; ?>">
                        <i class="fas <?php echo $icons[$cat] ?? 'fa-tag'; ?> mr-2"></i><?php echo strtoupper($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-24">
            <?php foreach($display_matches as $match): 
                $status = strtoupper($match['status'] ?? 'UPCOMING');
                $is_live = ($status === 'LIVE');
                $watch_url = $match['adfree_url'] ?? $match['dai_url'] ?? '#';
            ?>
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col group transition-all hover:shadow-2xl hover:-translate-y-2">
                <div class="relative aspect-[16/10] overflow-hidden">
                    <img src="<?php echo $match['src'] ?? ''; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    <div class="absolute top-5 right-5">
                        <?php if($is_live): ?>
                            <span class="bg-red-600 text-white text-[9px] font-black px-3 py-1.5 rounded-xl shadow-xl flex items-center uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 bg-white rounded-full mr-2 live-pulse"></span> LIVE
                            </span>
                        <?php else: ?>
                            <span class="bg-yellow-500 text-white text-[9px] font-black px-3 py-1.5 rounded-xl shadow-xl uppercase tracking-widest">
                                <i class="far fa-clock mr-1.5"></i> UPCOMING
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="p-7 flex-grow">
                    <h3 class="font-extrabold text-sm leading-tight mb-4 h-11 line-clamp-2"><?php echo htmlspecialchars($match['title'] ?? 'Event'); ?></h3>
                    <div class="flex items-center text-[10px] text-blue-600 dark:text-blue-400 font-black mb-3 uppercase tracking-widest">
                        <i class="fas <?php echo $icons[$match['event_category'] ?? ''] ?? 'fa-trophy'; ?> mr-2"></i>
                        <?php echo $match['event_category'] ?? 'Sports'; ?>
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold uppercase"><i class="far fa-calendar-check mr-2 text-purple-500"></i><?php echo $match['startTime'] ?? ''; ?></div>
                </div>

                <div class="px-7 pb-7 mt-auto">
                    <?php if($is_live): ?>
                        <a href="<?php echo htmlspecialchars($watch_url); ?>" target="_blank" class="block w-full text-center py-4 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-200 dark:shadow-none transition-all active:scale-95">Watch Live Now</a>
                    <?php else: ?>
                        <div class="w-full text-center py-4 bg-slate-50 dark:bg-slate-800/50 text-slate-400 text-[10px] font-black uppercase rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800 tracking-widest">Available Soon</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <a href="https://t.me/xfireflix" target="_blank" class="fixed bottom-8 right-8 w-16 h-16 bg-[#0088cc] text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-90 transition-all z-50">
        <i class="fab fa-telegram-plane text-3xl"></i>
    </a>

    <script>
        function updateClock() {
            const now = new Date();
            let h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            const strTime = `${h}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')} ${ampm} ${String(now.getDate()).padStart(2,'0')}-${String(now.getMonth()+1).padStart(2,'0')}-${now.getFullYear()}`;
            document.getElementById('clock').innerText = strTime;
        }
        setInterval(updateClock, 1000); updateClock();

        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            document.getElementById('theme-icon').className = isDark ? 'fas fa-sun text-lg' : 'fas fa-moon text-lg';
            localStorage.setItem('fancode_ui_theme', isDark ? 'dark' : 'light');
        }
        if(localStorage.getItem('fancode_ui_theme') === 'dark') {
            document.documentElement.classList.add('dark');
            document.getElementById('theme-icon').className = 'fas fa-sun text-lg';
        }
    </script>
</body>
</html>
