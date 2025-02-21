<?php
include 'includes/config.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM d4films_movies WHERE is_premium <= ?");
$stmt->execute([$_SESSION['is_premium'] ? 1 : 0]);
$movies = $stmt->fetchAll();
?>

<div class="container">
    <h1>All movies</h1>
    <?php if($_SESSION['is_premium']): ?>
        <div class="premium-badge">PREMIUM</div>
    <?php endif; ?>
    <div class="movie-grid">
        <?php foreach($movies as $movie): ?>
        <div class="movie-card">
            <div class="video-player-wrapper">
                <video class="custom-video-player">
                    <source src="assets/movies/<?= $movie['file_path'] ?>" type="video/mp4">
                    Your browser does not support the video player.
                </video>
                
                <div class="player-controls">
                    <div class="control-bar">
                        <button class="button-control play-pause">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="timeline">
                            <div class="progress"></div>
                        </div>
                        <div class="time-display">
                            <span class="current-time">00:00</span> / 
                            <span class="duration">00:00</span>
                        </div>
                        <div class="volume-control">
                            <button class="button-control volume-btn">
                                <i class="fas fa-volume-up"></i>
                            </button>
                            <input type="range" class="volume-slider" min="0" max="1" step="0.1" value="1">
                        </div>
                        <button class="button-control settings-btn">
                            <i class="fas fa-cog"></i>
                        </button>
                        <button class="button-control fullscreen">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                    
                    <div class="settings-menu">
                        <div class="speed-menu">
                            <h4>Playback speed:</h4>
                            <button class="speed-option" data-speed="0.5">0.5x</button>
                            <button class="speed-option" data-speed="1">1x</button>
                            <button class="speed-option" data-speed="1.5">1.5x</button>
                            <button class="speed-option" data-speed="2">2x</button>
                        </div>
                    </div>
                </div>
            </div>       

            <div class="movie-info">
                <h3><?= htmlspecialchars($movie['title']) ?></h3>
                <p><?= htmlspecialchars($movie['description']) ?></p>

                <?php if($movie['is_premium']): ?>
                    <div class="premium-tag">Premium</div>
                <?php endif; ?>
                
                <div class="movie-meta">
                    <span>HD 1080p</span>
                    <span>0h 0m 16s</span>
                    <span>Action</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    class VideoPlayer {
        constructor(wrapper) {
            this.wrapper = wrapper;
            this.video = wrapper.querySelector('video');
            this.controls = {
                playPause: wrapper.querySelector('.play-pause'),
                timeline: wrapper.querySelector('.timeline'),
                progress: wrapper.querySelector('.progress'),
                volumeSlider: wrapper.querySelector('.volume-slider'),
                volumeBtn: wrapper.querySelector('.volume-btn'),
                settingsBtn: wrapper.querySelector('.settings-btn'),
                fullscreen: wrapper.querySelector('.fullscreen'),
                currentTime: wrapper.querySelector('.current-time'),
                duration: wrapper.querySelector('.duration'),
                speedOptions: wrapper.querySelectorAll('.speed-option')
            };
            this.setupEventListeners();
            this.initializeVideo();
        }

        initializeVideo() {
            this.video.addEventListener('loadedmetadata', () => {
                this.controls.duration.textContent = this.formatTime(this.video.duration);
            });
        }

        setupEventListeners() {
            // Play/Pause
            this.controls.playPause.addEventListener('click', () => this.togglePlay());
            this.video.addEventListener('click', () => this.togglePlay());
            this.video.addEventListener('play', () => this.updatePlayButton());
            this.video.addEventListener('pause', () => this.updatePlayButton());

            // Timeline
            this.controls.timeline.addEventListener('click', (e) => this.seek(e));
            this.video.addEventListener('timeupdate', () => this.updateProgress());

            // Volume
            this.controls.volumeSlider.addEventListener('input', (e) => this.setVolume(e.target.value));
            this.controls.volumeBtn.addEventListener('click', () => this.toggleMute());

            // Fullscreen
            this.controls.fullscreen.addEventListener('click', () => this.toggleFullscreen());

            // Playback Speed
            this.controls.settingsBtn.addEventListener('click', (e) => this.toggleSettingsMenu(e));
            this.controls.speedOptions.forEach(option => {
                option.addEventListener('click', (e) => this.setSpeed(e.target.dataset.speed));
            });
        }

        togglePlay() {
            this.video.paused ? this.video.play() : this.video.pause();
        }

        updatePlayButton() {
            const icon = this.video.paused ? 'fa-play' : 'fa-pause';
            this.controls.playPause.innerHTML = `<i class="fas ${icon}"></i>`;
        }

        seek(e) {
            const rect = this.controls.timeline.getBoundingClientRect();
            const pos = (e.clientX - rect.left) / rect.width;
            this.video.currentTime = pos * this.video.duration;
        }

        updateProgress() {
            const progress = (this.video.currentTime / this.video.duration) * 100;
            this.controls.progress.style.width = `${progress}%`;
            this.controls.currentTime.textContent = this.formatTime(this.video.currentTime);
        }

        setVolume(value) {
            this.video.volume = value;
            this.video.muted = false;
            this.controls.volumeBtn.innerHTML = value == 0 ? 
                '<i class="fas fa-volume-mute"></i>' : 
                '<i class="fas fa-volume-up"></i>';
        }

        toggleMute() {
            this.video.muted = !this.video.muted;
            this.controls.volumeSlider.value = this.video.muted ? 0 : this.video.volume;
            this.setVolume(this.video.muted ? 0 : this.video.volume);
        }

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                this.wrapper.requestFullscreen().catch(err => {
                    alert(`Error with fullscreen: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }

        toggleSettingsMenu(e) {
            const menu = this.wrapper.querySelector('.settings-menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            e.stopPropagation();
        }

        setSpeed(speed) {
            this.video.playbackRate = speed;
            this.wrapper.querySelector('.settings-menu').style.display = 'none';
        }

        formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            seconds = Math.floor(seconds % 60);
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
    }

    document.querySelectorAll('.video-player-wrapper').forEach(wrapper => {
        new VideoPlayer(wrapper);
    });

    document.addEventListener('click', (e) => {
        document.querySelectorAll('.settings-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>