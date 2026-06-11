            </main>
        </div>
    </div>

    <!-- Toggle Sidebar & Dark Mode Script -->
    <script>
        // Mobile Sidebar Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuCloseBg = document.getElementById('mobileMenuCloseBg');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.remove('hidden');
            });
            
            mobileMenuCloseBg.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        }

        // Dark Mode Logic
        const adminThemeToggle = document.getElementById('adminThemeToggle');
        const adminSunIcon = document.getElementById('adminSunIcon');
        const adminMoonIcon = document.getElementById('adminMoonIcon');

        function updateIcons() {
            if (document.documentElement.classList.contains('dark')) {
                adminSunIcon.classList.remove('hidden');
                adminMoonIcon.classList.add('hidden');
            } else {
                adminSunIcon.classList.add('hidden');
                adminMoonIcon.classList.remove('hidden');
            }
        }

        // Check theme initially
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        updateIcons();

        if (adminThemeToggle) {
            adminThemeToggle.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                updateIcons();
            });
        }
    </script>
</body>
</html>
