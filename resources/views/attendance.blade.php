<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Kehadiran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-200">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200" x-data="attendanceApp()">
        <div class="flex h-screen">
            <!-- Sidebar Kiri - Input Form -->
            <div class="w-1/3 bg-white dark:bg-gray-800 shadow-lg border-r border-gray-200 dark:border-gray-700 flex flex-col">
                <!-- Header Input -->
                <div class="bg-blue-600 dark:bg-blue-700 text-white p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold flex items-center">
                            <i class="fas fa-calendar-plus mr-3"></i>
                            Input Kehadiran
                        </h2>
                        <!-- Dark Mode Toggle -->
                        <button
                            @click="toggleDarkMode"
                            class="p-2 rounded-lg bg-blue-500 hover:bg-blue-400 transition duration-200"
                        >
                            <i x-show="!darkMode" class="fas fa-moon"></i>
                            <i x-show="darkMode" class="fas fa-sun"></i>
                        </button>
                    </div>
                </div>

                <!-- Form Input -->
                <div class="flex-1 p-6 overflow-y-auto">
                    <form @submit.prevent="submitAttendance" class="space-y-6">
                        @csrf

                        <!-- Input Tanggal -->
                        <div class="space-y-2">
                            <label for="date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-calendar mr-2 text-blue-500"></i>
                                Tanggal
                            </label>
                            <input
                                type="date"
                                id="date"
                                x-model="form.date"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                required
                            >
                        </div>

                        <!-- Status Kehadiran (Checkbox) -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-user-check mr-2 text-blue-500"></i>
                                Status Kehadiran
                            </label>
                            <div class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
                                <label class="flex items-center cursor-pointer group">
                                    <div class="relative">
                                        <input
                                            type="checkbox"
                                            x-model="form.is_present"
                                            class="sr-only"
                                        >
                                        <!-- Custom Checkbox -->
                                        <div
                                            @click="form.is_present = !form.is_present"
                                            class="w-6 h-6 border-2 rounded-md transition duration-200 flex items-center justify-center cursor-pointer"
                                            :class="form.is_present
                                                ? 'bg-blue-600 border-blue-600 text-white'
                                                : 'bg-white dark:bg-gray-600 border-gray-300 dark:border-gray-500 hover:border-blue-400'"
                                        >
                                            <i x-show="form.is_present" class="fas fa-check text-sm"></i>
                                        </div>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition duration-200">
                                        <i class="fas fa-user-check text-blue-500 mr-2"></i>
                                        <span x-text="form.is_present ? 'Hadir' : 'Tidak Hadir'"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Aktivitas -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-tasks mr-2 text-blue-500"></i>
                                Aktivitas
                            </label>

                            <!-- List Aktivitas -->
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                <template x-for="(activity, index) in form.activities" :key="index">
                                    <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <input
                                            type="text"
                                            x-model="activity.name"
                                            placeholder="Masukkan aktivitas..."
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm placeholder-gray-400 dark:placeholder-gray-500"
                                        >
                                        <button
                                            type="button"
                                            @click="removeActivity(index)"
                                            class="px-3 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition duration-200"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Aktivitas -->
                            <button
                                type="button"
                                @click="addActivity"
                                class="w-full py-2 px-4 border-2 border-dashed border-blue-300 dark:border-blue-600 text-blue-600 dark:text-blue-400 rounded-lg hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition duration-200 flex items-center justify-center"
                            >
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Aktivitas
                            </button>
                        </div>

                        <!-- Tombol Submit -->
                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full bg-blue-600 dark:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                        >
                            <template x-if="loading">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                            </template>
                            <template x-if="!loading">
                                <i class="fas fa-save mr-2"></i>
                            </template>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan Kehadiran'"></span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Area Kanan - History -->
            <div class="flex-1 flex flex-col">
                <!-- Header History -->
                <div class="bg-blue-700 dark:bg-blue-800 text-white p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold flex items-center">
                            <i class="fas fa-history mr-3"></i>
                            Riwayat Kehadiran
                        </h2>
                        <div class="flex items-center gap-4">
                            <input
                                type="date"
                                x-model="selectedDate"
                                @change="filterHistory"
                                class="px-3 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-blue-400 dark:border-blue-600 rounded-md focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800"
                            >
                            <button
                                @click="refreshHistory"
                                class="px-4 py-2 bg-blue-600 dark:bg-blue-600 hover:bg-blue-500 dark:hover:bg-blue-500 rounded-md transition duration-200"
                            >
                                <i class="fas fa-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Content History -->
                <div class="flex-1 p-6 overflow-y-auto bg-gray-50 dark:bg-gray-900">
                    <!-- Loading State -->
                    <div x-show="loadingHistory" class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">Memuat riwayat...</p>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!loadingHistory && history.length === 0" class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <i class="fas fa-calendar-times text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400 mb-2">Tidak Ada Data</h3>
                            <p class="text-gray-400 dark:text-gray-500">Belum ada riwayat kehadiran untuk tanggal yang dipilih</p>
                        </div>
                    </div>

                    <!-- History Cards -->
                    <div x-show="!loadingHistory && history.length > 0" class="space-y-4">
                        <template x-for="record in history" :key="record.id">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition duration-200">
                                <!-- Header Card -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center"
                                             :class="record.is_present ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-gray-100 dark:bg-gray-700'">
                                            <i :class="record.is_present ? 'fas fa-check text-blue-600 dark:text-blue-400' : 'fas fa-times text-gray-500 dark:text-gray-400'"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 dark:text-gray-200" x-text="formatDate(record.date)"></h4>
                                            <p class="text-sm"
                                               :class="record.is_present ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'"
                                               x-text="record.is_present ? 'Hadir' : 'Tidak Hadir'">
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span x-text="formatTime(record.created_at)"></span>
                                    </div>
                                </div>

                                <!-- Aktivitas -->
                                <div x-show="record.activities && record.activities.length > 0">
                                    <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                        <i class="fas fa-tasks mr-2 text-blue-500"></i>
                                        Aktivitas:
                                    </h5>
                                    <div class="space-y-2">
                                        <template x-for="activity in record.activities" :key="activity.id">
                                            <div class="flex items-center gap-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                                                <i class="fas fa-check-circle text-blue-500 text-sm"></i>
                                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="activity.name"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- No Activities -->
                                <div x-show="!record.activities || record.activities.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Tidak ada aktivitas yang dicatat
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed top-4 right-4 z-50">
            <div class="px-6 py-4 rounded-lg shadow-lg"
                 :class="toast.type === 'success' ? 'bg-blue-500 text-white' : 'bg-red-500 text-white'">
                <div class="flex items-center">
                    <i :class="toast.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'" class="mr-3"></i>
                    <span x-text="toast.message"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function attendanceApp() {
            return {
                // Form data
                form: {
                    date: new Date().toISOString().split('T')[0],
                    is_present: false,
                    activities: [{ name: '' }]
                },

                // UI states
                loading: false,
                loadingHistory: false,
                selectedDate: new Date().toISOString().split('T')[0],
                darkMode: localStorage.getItem('darkMode') === 'true' || false,

                // Data
                history: [],

                // Toast notification
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                init() {
                    this.loadHistory();
                    this.applyDarkMode();
                },

                // Dark mode methods
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    this.applyDarkMode();
                    localStorage.setItem('darkMode', this.darkMode);
                },
 
                applyDarkMode() {
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },

                // Form methods
                addActivity() {
                    this.form.activities.push({ name: '' });
                },

                removeActivity(index) {
                    if (this.form.activities.length > 1) {
                        this.form.activities.splice(index, 1);
                    }
                },

                async submitAttendance() {
                    this.loading = true;

                    try {
                        // Filter out empty activities
                        const validActivities = this.form.activities.filter(activity => activity.name.trim() !== '');

                        const formData = {
                            date: this.form.date,
                            present: this.form.is_present,
                            activities: validActivities,
                            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        };

                        const response = await fetch('/api/attendance', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': formData._token
                            },
                            body: JSON.stringify(formData)
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            this.showToast(result.message || 'Kehadiran berhasil disimpan!', 'success');
                            this.resetForm();
                            this.loadHistory();
                        } else {
                            throw new Error(result.message || 'Gagal menyimpan data');
                        }
                    } catch (error) {
                        this.showToast('Terjadi kesalahan: ' + error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                resetForm() {
                    this.form = {
                        date: new Date().toISOString().split('T')[0],
                        is_present: false,
                        activities: [{ name: '' }]
                    };
                },

                // History methods
                async loadHistory() {
                    this.loadingHistory = true;

                    try {
                        const response = await fetch(`/api/attendance/history?date=${this.selectedDate}`);
                        const result = await response.json();

                        if (response.ok) {
                            this.history = result;
                        } else {
                            throw new Error(result.message || 'Gagal memuat data');
                        }
                    } catch (error) {
                        console.error('Error loading history:', error);
                        this.showToast('Gagal memuat riwayat: ' + error.message, 'error');
                        this.history = [];
                    } finally {
                        this.loadingHistory = false;
                    }
                },

                async filterHistory() {
                    await this.loadHistory();
                },

                async refreshHistory() {
                    await this.loadHistory();
                    this.showToast('Riwayat berhasil dimuat ulang!', 'success');
                },

                // Utility methods
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                },

                formatDate(dateString) {
                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    return new Date(dateString).toLocaleDateString('id-ID', options);
                },

                formatTime(dateString) {
                    return new Date(dateString).toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                // Mock data generator for demo
                generateMockData() {
                    return [
                        {
                            id: 1,
                            date: this.selectedDate,
                            is_present: true,
                            created_at: new Date().toISOString(),
                            activities: [
                                { id: 1, name: 'Menghadiri meeting pagi' },
                                { id: 2, name: 'Menyelesaikan laporan bulanan' },
                                { id: 3, name: 'Review kode program' }
                            ]
                        },
                        {
                            id: 2,
                            date: this.selectedDate,
                            is_present: false,
                            created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(),
                            activities: []
                        }
                    ].filter(record => record.date === this.selectedDate);
                }
            }
        }
    </script>
</body>
</html>
