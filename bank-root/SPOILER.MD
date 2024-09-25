# Daftar Temuan di Aplikasi BANK Root
Di bawah ini adalah daftar temuan IDOR yang ada di aplikasi BANK Root
1. Mengganti initial balance yang harusnya Rp 20.000 menjadi berapapun yang diinginkan dengan mengganti request payload initial_balance menjadi berapapun yang diinginkan
2. Membuat saldo akun lain berkurang dan akun penyerang bertambah dengan mengganti value transfer menjadi < 0 (ex: -10000, value tersebut dapat membuat akun lain berkurang menjadi -10rb dan akun penyerang bertambah menjadi +10rb)
3. Melakukan transfer dari akun pengguna lain ke akun diri sendiri dengan mengganti from_account menjadi bank account pengguna lain dan to_account menjadi bank account penyerang