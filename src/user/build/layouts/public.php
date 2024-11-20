<!DOCTYPE html>
<html lang="en" class="h-100" data-bs-theme="dark">
    <head>
        @yield("top-head")
        <!-- Head -->
        <?php include_once(sm::Dir("Dom") . "head.php")?>
        @yield("low-head")
    </head>
    @yield("pre-body")
    <body class="d-flex flex-column h-100">
        <header>
            @yield("top-header")
            <!-- Header -->
            <?php include_once(sm::Dir("Dom") . "header.php")?>
            @yield("low-header")
        </header>
        @yield("pre-main")
        <main>
            @yield("content")
        </main>
        @yield("post-main")
        <footer class="footer mt-auto py-3">
                @yield("top-footer")
                <!-- Footer -->
                <?php include_once(sm::Dir("Dom") . "footer.php")?>
                @yield("low-footer")
        </footer>
    </body>
    @yield("post-body")
    @yield("pre-foot")
    <!-- Foot (Scripts etc) -->
    <?php include_once(sm::Dir("Dom") . "foot.php")?>
    @yield("post-foot")
</html>