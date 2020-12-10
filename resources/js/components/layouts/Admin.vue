<template>
    <vuescroll :ops="ops">
        <div class="wrapper d-flex flex-column">
            <div class="overlay position-absolute"></div>
            <div class="main d-flex flex-column flex-grow-1">
                <header class="container-fluid d-flex justify-content-between">
                    <nav class="navbar container navbar-expand-lg navbar-light w-100 px-0">
                        <a class="navbar-brand text-white" href="#">Адмін панель</a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNavDropdown">
                            <div class="d-lg-flex d-block justify-content-between w-100">
                                <ul class="navbar-nav d-none d-lg-flex">
                                    <li class="nav-item" data-toggle="collapse">
                                        <router-link tag="div" :to="{ name: 'admin.users'}" class="nav-link px-3" active-class="active">Користувачі</router-link>
                                    </li>
                                    <li class="nav-item" data-toggle="collapse">
                                        <router-link tag="div" :to="{ name: 'admin.events'}" class="nav-link px-3" active-class="active">Події</router-link>
                                    </li>
                                    <li class="nav-item" data-toggle="collapse">
                                        <router-link tag="div" :to="{ name: 'admin.vote'}" class="nav-link px-3" active-class="active">Голосування</router-link>
                                    </li>
                                </ul>
                                <ul class="navbar-nav d-flex d-lg-none">
                                    <li class="nav-item" data-toggle="collapse" data-target="#navbarNavDropdown">
                                        <router-link tag="div" :to="{ name: 'admin.users'}" class="nav-link px-3" active-class="active">Користувачі</router-link>
                                    </li>
                                    <li class="nav-item" data-toggle="collapse" data-target="#navbarNavDropdown">
                                        <router-link tag="div" :to="{ name: 'admin.events'}" class="nav-link px-3" active-class="active">Події</router-link>
                                    </li>
                                    <li class="nav-item" data-toggle="collapse" data-target="#navbarNavDropdown">
                                        <router-link tag="div" :to="{ name: 'admin.vote'}" class="nav-link px-3" active-class="active">Голосування</router-link>
                                    </li>
                                </ul>
                                <div class="profile d-flex align-items-center">
                                    <img :src="$auth.user().avatar_original" alt="avatar" rel="noreferrer" class="avatar m-2">
                                    <div class="m-2 text-white">{{ $auth.user().name }}</div>
                                    <div @click.prevent="$auth.logout()" class="logout m-2 text-white">Вихід</div>
                                </div>
                            </div>
                        </div>
                    </nav>

                </header>
                <div class="d-flex flex-grow-1 flex-column">
                    <router-view></router-view>
                </div>
                <footer class="container-fluid py-4">
                    <div class="container-fluid">
                        <span class="text-white">© 2020 Офіційний сайт Національний університет водного господарства та природокористування</span>
                    </div>
                </footer>
            </div>
        </div>
    </vuescroll>
</template>

<script>

import vuescroll from "vuescroll";

export default {
    components: {
        vuescroll
    },
    data() {
        return {
            ops: {
                vuescroll: {},
                scrollPanel: {},
                rail: {},
                bar: {
                    onlyShowBarOnScroll: true,
                    background: "#ED6E00",
                    keepShow: true,
                }
            }
        };
    },
}
</script>

<style scoped lang="scss">

.wrapper {
    background-image: url(/img/background.jpg);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;

    min-height: 100vh;

    .overlay {
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.5);
        z-index: 0;
    }

    .main {
        z-index: 1;

        header {
            background-color: #256589;

            .nav-item {
                .nav-link {
                    color: white!important;
                    font-size: 12px;
                    text-transform: uppercase;

                    &.active {
                        background: white!important;
                        color: #256589!important;
                    }

                    &:hover {
                        background: white!important;
                        color: #256589!important;
                        cursor: pointer;
                    }
                }
            }

            .avatar {
                width: 20px;
                height: 20px;
            }

            .logout {
                cursor: pointer;
            }
        }

        footer {
            background-color:  #256589;
        }
    }
}

</style>
