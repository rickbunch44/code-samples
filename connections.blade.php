<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('inc.head')

<!-- Styles -->
    <style>
        .col-sm-12, .col-sm-3, .col-sm-4 {
            padding: 0;
        }

        .clearfix {
            width: 100%;
            height: 1px;
            visibility: hidden;
            margin: 15px 0;
        }

        .search-container {
            position: relative;
            width: 315px;
        }

        .search-button {
            padding: 5px 13px;
            color: #111;
            background-color: whitesmoke;
            border: 2px solid #18a2d9;
            margin-top: 15px;
            font-family: 'Nunito';
            font-weight: bold;
            font-size: 14px;
        }

        .search-button:hover {
            background-color: #18a2d9;
            border: 2px solid #1894c5;
            color: #fefefe;
        }

        .search-bar {
            font-family: 'Nunito';
            font-size: 16px;
            margin: 15px 0;
            padding: 10px 35px 10px 15px;
            background-color: #f6f6f6;
            border: 1px solid #CCC;
            width: 100% !important;
            min-width: 0 !important;
        }

        .search-icon {
            position: absolute;
            right: 5px;
            top: 20px;
            width: 30px;
            height: 30px;
            margin-left: 30px;
        }

        .search-filters {
            width: 315px;
            height: 50px;
            background-color: #D3D3D3;
            border-radius: 5px;
        }

        .search-filter-button:first-of-type {
            border-radius: 5px 0 0 5px;
        }

        .search-filter-button:last-of-type {
            border-radius: 0 5px 5px 0;
        }

        .search-filter-button:hover {
            background-color: #C8C8C8;
        }

        .search-filter-button {
            padding: 15px 0;
            cursor: pointer;
        }

        .search-filter-icon {
            display: block;
            margin: 0 auto;
            width: 20px;
            height: 20px;
        }

        .filter-on {
            background-color: #ADADAD !important;
        }

        #user-listings {
            background-color: #E3E3E3;
            box-shadow: 0 0 2px #838383;
            padding: 15px;
        }

        .user-container {
            cursor: pointer;
            padding: 12px 20px;
            border-bottom: 1px solid #CCC;
            background-color: #EAEAEA;
            width: 100%;
            height: 75px;
        }

        .user-container:nth-of-type(odd) {
            background-color: #EFEFEF;
        }

        .user-container:hover {
            background-color: #EEE;
        }

        .user-profile-image-container {
            padding: 0 15px;
        }

        .user-profile-name-container {
            padding: 0;
        }

        .user-profile-image {
            vertical-align: middle;
            vertical-align: -webkit-baseline-middle;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 1px #CCC;
        }

        .user-profile-name {
            margin-left: 12px;
            margin-bottom: 0;
            margin-top: 3px;
            font-family: 'Nunito', sans-serif;
            display: inline-block;
            font-size: 20px;
            font-weight: bold;
            color: #444;
        }

        .user-profile-title {
            margin-left: 12px;
            margin-top: -7px;
            margin-bottom: 0;
            font-size: 16px;
            color: #444;
            font-style: italic;
        }

        #suggested-connections .secondary-heading {
            font-size:.9em;
            margin-bottom:15px;
            color:#666;
            width:100%;
        }

        .row .suggested-connection-container:last-of-type {
            padding:0;
        }
        .suggested-connection-container {
            width: 100%;
            padding-right:3px;
            padding-bottom:3px;
        }

        .suggested-connection-container:hover {
            background-color:#EEE;
            cursor: pointer;
        }

        .suggested-connection {
            border: 1px solid #CCC;
            border-radius: 2px;
            padding: 8px;
        }

        .suggested-connection img {
            width:100%;
            max-width:80px;
            border-radius: 5px;
        }

        .suggested-connection-name {
            text-align: center;
            margin:5px auto 0;
            color:#333;
        }

        .suggested-connection-name:nth-of-type(2) {
            margin:-5px auto 0;
        }

        .suggested-connection-title {
            font-size:.7em;
            color:#888;
            word-wrap: break-word;
        }

        .slide-fade-enter-active {
            transition: all 1.7s ease;
        }

        .slide-fade-leave-active {
            transition: all 1.7s cubic-bezier(1.0, 0.5, 0.8, 1.0);
        }

        .slide-fade-enter, .slide-fade-leave-to
            /* .slide-fade-leave-active below version 2.1.8 */
        {
            transform: translateX(30px);
            opacity: 0;
        }

        @media screen AND (max-width: 480px) {
            #user-listings {
                margin: -5px;
                padding: 0;
            }

            .user-container .user-profile-name {
                margin-left: 0;
                margin-bottom: 20px;
                font-size: 17px;
            }

            .user-container .user-profile-title {
                margin-left: 0;
                font-size: 14px;
            }

            .user-container .user-profile-image {
                display: none;
                margin-bottom: 8px;
            }

            .user-send-button {
                top: 10px;
                right: 15px;
                padding: 2px 4px;
            }
        }
    </style>
</head>

<body>

<div class="flex-center position-ref full-height">
    <div id="app" class="content">
        @include('inc.header')

        <div id="page-container">
            <div class="row">
                <div id="user-search" class="col-xs-12 col-sm-4 offset-2">
                    <div class="row search-container">
                        <input v-model="pagination.query" type="text" class="search-bar"/>
                        <img src="https://cdn1.iconfinder.com/data/icons/hawcons/32/698627-icon-111-search-512.png"
                             alt="Search"
                             class="search-icon">
                    </div>
                    <div class="row search-filters">
                        <div @click="filterBy.favorites = !filterBy.favorites"
                             :class="{'filter-on': filterBy.favorites }"
                             class="col-sm-3 search-filter-button">
                            <img
                                src="https://cdn2.iconfinder.com/data/icons/crystalproject/crystal_project_256x256/apps/keditbookmarks.png"
                                alt="" class="search-filter-icon">
                        </div>
                        <div @click="filterBy.company = !filterBy.company" :class="{'filter-on': filterBy.company }"
                             class="col-sm-3 search-filter-button">
                            <img src="https://www.shareicon.net/download/2016/08/24/820078_business_512x512.png"
                                 alt="" class="search-filter-icon">
                        </div>
                        <div @click="filterBy.alpha_a = !filterBy.alpha_a" :class="{'filter-on': filterBy.alpha_a }"
                             class="col-sm-3 search-filter-button">
                            <img src="https://cdn3.iconfinder.com/data/icons/text-icons-1/512/BT_sort_az-512.png"
                                 alt="" class="search-filter-icon">
                        </div>
                        <div @click="filterBy.alpha_z = !filterBy.alpha_z" :class="{'filter-on': filterBy.alpha_z }"
                             class="col-sm-3 search-filter-button">
                            <img src="https://cdn3.iconfinder.com/data/icons/text-icons-1/512/BT_sort_az-512.png"
                                 alt="" class="search-filter-icon">
                        </div>
                        <button @click="loadUsers(true)" class="search-button">Search</button>

                        <div class="clearfix"></div>

                        <div class="row" id="suggested-connections">
                            <h5 class="secondary-heading">Suggested Connections</h5>
                            <div @click="goToProfile(user.uuid)" v-for="user in suggestedUsers" class="suggested-connection-container col-sm-4">
                                <div class="suggested-connection">
                                    <img :src="user.profile_pic" :title="user.fname + ' ' + user.lname" class="center-align">
                                    <p v-text="user.fname" class="suggested-connection-name"></p>
                                    <p v-text="user.lname" class="suggested-connection-name"></p>
                                    <p v-text="user.title" class="suggested-connection-title center-align"></p>
                                    <p v-text="user.company" class="suggested-connection-title center-align"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <transition name="slide-fade">
                    <div v-cloak v-show="showResults" id="user-listings" class="col-xs-12 col-sm-4">
                        <div v-if="!users || users.count == 0" class="user-container relative">
                            <p class="empty center-align" style="margin-top:25px;">No results to show.</p>
                        </div>
                        <div v-if="users" @click="goToProfile(user.uuid)" v-for="user in users"
                             class="user-container relative row">
                            <div class="user-profile-image-container col-sm-2">
                                <img class="user-profile-image" :src="user.profile_pic"/>
                            </div>

                            <div class="user-profile-name-container col-sm-10">
                                <p v-text="user.fname + ' ' + user.lname" class="user-profile-name"></p>
                                <p v-text="user.title + ', ' + user.company" class="user-profile-title"></p>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>

        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>

<script>
    const app = new Vue({
        el: "#app",
        mounted() {
            this.loadSuggestedConnections();
        },
        data: () => {
            return {
                users: null,
                suggestedUsers: null,
                showResults: false,
                filterBy: {
                    favorites: false,
                    company: false,
                    alpha_a: false,
                    alpha_z: false,
                },
                pagination: {
                    query: null,
                    page: 1,
                    totalUsers: null,
                    totalPages: null,
                    size: 10,
                }
            }
        },
        methods: {
            loadSuggestedConnections() {
                let self = this;
                let route = 'load_suggested_connections';
                axios.post(route).then(function (response) {
                    Vue.set(self, "suggestedUsers", response.data.users);
                }).catch(function () {
                    alert('failure!');
                });
            },
            loadUsers: (forced = null) => {
                let route = 'load_users';
                axios.post(route,
                    {
                        connectionsOnly: false,
                        filterBy: app.filterBy,
                        pagination: app.pagination,
                    }).then(function (response) {
                    Vue.set(app, "users", response.data.users);
                    // Vue.set(app.pagination, "totalUsers", result.data.totalUsers);
                    // Vue.set(app.pagination, "totalPages", result.data.totalPages);
                    Vue.nextTick(function () {
                        app.users.forEach(function (user, index) {
                            Vue.set(app.users[index], "showMenu", false);
                        });
                    });
                }).catch(function () {
                    alert('failure!');
                }).then(function () {
                    Vue.set(app, "showResults", true);
                    if (forced) {
                    }
                });
            },
            goToProfile(userId) {
                document.location.href = '{{ url("/profile") }}' + '/' + userId;
                Vue.set(app.pagination, "page", 1);
            }
        }
    });
</script>
</div>
</body>
</html>
