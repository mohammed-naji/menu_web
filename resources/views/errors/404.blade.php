<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    class="light scroll-smooth">

<head>
    <meta charset="UTF-8">
    <title>404</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Css -->
    <!-- Main Css -->
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="dark:bg-slate-900">
    <!-- Start -->
    <section class="relative bg-amber-500/5">
        <div class="container-fluid relative">
            <div class="grid grid-cols-1">
                <div class="flex flex-col min-h-screen justify-center md:px-10 py-10 px-4">

                    <div class="title-heading text-center my-auto">
                        <h4
                            class="md:text-[160px] text-8xl md:leading-normal leading-normal text-slate-900/10 dark:text-white/10">
                            404!</h4>
                        <h1 class="mt-8 mb-6 md:text-5xl text-3xl font-bold">{{ __('Page Not Found') }}?</h1>
                        <div class="mt-4">
                            <a href="/{{ request()->segment(1) }}"
                                class="py-2 px-5 inline-block font-medium tracking-wide border align-middle duration-500 text-base text-center bg-amber-500 hover:bg-amber-600 border-amber-500 hover:border-amber-600 text-white rounded-md">{{ __('Back to Menu') }}</a>
                        </div>
                    </div>
                </div>
            </div><!--end grid-->
        </div><!--end container-->
    </section><!--end section-->
    <!-- End -->
</body>

</html>
