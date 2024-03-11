<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(resource_path('css/app.css'));
    @vite(resource_path('js/app.js'));
</head>
<body class="flex items-center justify-center min-h-screen bg-background">
    <div class="max-w-sm rounded overflow-hidden shadow-lg m-auto">
        <div class="px-6 py-4">
          <div class="font-bold text-xl mb-2 text-title">The Coldest Sunset</div>
          <p class="text-pText text-base">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatibus quia, nulla! Maiores et perferendis eaque, exercitationem praesentium nihil.
          </p>
        </div>
        <div class="px-6 pt-4 pb-2">
          <span class="inline-block bg-spanBG rounded-full px-3 py-1 text-sm font-semibold text-spanText mr-2 mb-2">#photography</span>
          <span class="inline-block bg-spanBG rounded-full px-3 py-1 text-sm font-semibold text-spanText mr-2 mb-2">#travel</span>
          <span class="inline-block bg-spanBG rounded-full px-3 py-1 text-sm font-semibold text-spanText mr-2 mb-2">#winter</span>
        </div>
        <button class="modeToggler bg-spanBG">Toggle mode</button>
      </div>
</body>
</html>
