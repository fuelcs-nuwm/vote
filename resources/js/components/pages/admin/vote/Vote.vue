<template>
    <div class="content container-fluid py-3 flex-grow-1 d-flex flex-column">
        <h2>Керування голосуванням</h2>
        <div class="row flex-grow-1 d-flex">
            <div class="col-lg-4 d-flex flex-grow-1 flex-column">
                <p>Доданий HTML:</p>
                <div id="iframe-wrapper" v-html="embedHtml" class=" flex-grow-1" ></div>
                <div class="input-group mb-3">
                    <textarea
                        type="text"
                        class="form-control"
                        placeholder="<HTML>"
                        v-model="embedHtmlModel"
                        rows="6"
                    ></textarea>
                </div>
                <button class="btn btn-secondary" @click="saveEmbedHtml">Додати HTML (відео)</button>
            </div>
            <div class="col-lg-5 main-section">
                <vuescroll :ops="ops">
                    <div class="form-group d-flex flex-column">
                        <p>Вибір події:</p>
                        <div class="d-flex">
                            <div class="flex-grow-1 mr-3">
                                <select class="form-control" v-model="selectedEvent">
                                    <option v-for="event in events" :value="event">{{ event.title}}</option>
                                </select>
                            </div>
                            <button class="btn btn-success" @click="doActivateEvent">Активувати подію</button>
                        </div>
                    </div>
                    <p>Активна подія:</p>
                    <div class="input-group mb-3">
                        <input
                            v-if="!activeEvent"
                            type="text" class="form-control"
                            :value="'немає активної події'"
                            readonly
                        >
                        <input
                            v-if="activeEvent"
                            type="text" class="form-control"
                            v-model="activeEvent.title"
                            readonly
                        >
                        <div class="input-group-append" @click="doDeactivateEvent">
                            <span
                                class="input-group-text bg-secondary"
                                :class="{'bg-danger': activeEvent}"
                            >Деактивувати</span>
                        </div>
                    </div>
                    <div v-if="activeEvent">

                        <p>
                            <span></span>Зареєстровано: {{ registeredUsers.length }} користувачів
                            <button @click="isShowRregisteredUsersModal = true" class="btn btn-secondary">Показати</button>
                        </p>

                        <p>Запитання:</p>

                        <button class="btn btn-secondary mb-3 mr-3" @click="isAddNewQuestion = !isAddNewQuestion">Додати запитання</button>

                        <div v-if="isAddNewQuestion">

                            <form @submit.prevent="storeQuestion">
                                <div class="input-group mb-3">
                                    <textarea
                                        type="text"
                                        class="form-control"
                                        placeholder="Нове запитання"
                                        v-model="newQuestion.title"
                                        rows="3"
                                    ></textarea>
                                    <div class="input-group-append">
                                        <span type="submit" class="input-group-text" @click="storeQuestion">Додати запитання</span>
                                    </div>
                                </div>
                            </form>
                            <div
                                v-if="!$v.newQuestion.title.required && $v.newQuestion.title.$dirty "
                                class="text-danger"
                            >Введіть назву
                            </div>
                        </div>

                        <div v-for="question in questions">
                            <div class="alert alert-secondary">
                                {{ question.title }}
                            </div>

                            <div v-if="activeVote && activeVote.question_id == question.id" class="progress mb-3">
                                <div
                                    class="progress-bar" role="progressbar"
                                    :style="{width: timerWidth + '%'}"
                                    :aria-valuenow="timerWidth"
                                    aria-valuemin="0"
                                    :aria-valuemax="activeVote.vote_time">

                                </div>
                            </div>

                            <div class="d-flex mb-3">
                                <button class="btn btn-secondary mr-3" @click="newVote(question.id)">Нове голосування</button>
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="mr-3">Тривалість (сек):</span>
                                    <select class="form-control w-auto" v-model="voteTime" @change="saveVoteTime">
                                        <option v-for=" voteTime in voteTimesList" :value="voteTime">{{ voteTime }}</option>
                                    </select>
                                </div>
                            </div>

                            <div v-for="vote in question.votes">
                                <div class="border border-secondary p-3 mb-3 bg-light">
                                    <div>Результат голосування о {{vote.finished_at}}</div>
                                    <div>Так - {{vote.answer_yes}}</div>
                                    <div>Ні - {{vote.answer_no}}</div>
                                    <div>Утримався - {{vote.answer_abstained}}</div>
                                    <div>Не голосували - {{vote.didnt_vote}}</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </vuescroll>
            </div>
            <div class="col-lg-3 flex-grow-1 d-flex flex-column">
                <p>Чат:</p>
                <Chat></Chat>
            </div>
        </div>
        <div
            v-if="isShowRregisteredUsersModal"
            class="registered-users-modal d-flex flex-column position-fixed p-3 bg-white"
        >
            <div class="header d-flex p-3 mb-3 align-items-center">
                <div class="flex-grow-1 text-white">
                    <h5>Зареєстровані користувачі ( {{registeredUsers.length }} )</h5>
                    <span v-for="groups in registeredUsersGroups" class="mr-3">{{groups.name}} - ({{groups.count}})</span>
                </div>
                <button class="btn btn-secondary" @click="isShowRregisteredUsersModal = !isShowRregisteredUsersModal">Закрити</button>
            </div>
            <div class="flex-grow-1">
                <vuescroll :ops="opsUsers">
                    <div class="alert alert-secondary" v-for="user in registeredUsers">
                        <b>{{ user.user.name }}</b> - <span>{{ user.user.email }}</span> - <span> {{ user.date }} </span>
                        <span v-for="group in user.user.groups" class="badge badge-secondary">{{group.name}}</span>
                    </div>
                </vuescroll>
            </div>
        </div>
    </div>
</template>

<script>
import {mapMutations} from "vuex";
import {clone as _clone, find as _find} from 'lodash';
import vuescroll from "vuescroll";
import {maxLength, required} from "vuelidate/lib/validators";
import Chat from "../../../common/Chat";

export default {
    components: {
        vuescroll,
        Chat
    },
    data() {
        return {
            isShowRregisteredUsersModal: false,
            isAddNewQuestion: false,
            events: [],
            registeredUsers: [],
            registeredUsersGroups: [],
            questions: [],
            selectedEvent: null,
            activeEvent: null,
            embedHtml: '',
            embedHtmlModel: '',
            voteTime: 0,
            voteTimesList: [10,20,30,40,50,60],
            newQuestion: {
                title: ""
            },

            activeVote: null,
            timerId: null,
            timerCurrentTime: null,
            timerWidth: null,

            ops: {
                vuescroll: {},
                scrollPanel: {},
                rail: {},
                bar: {
                    onlyShowBarOnScroll: true,
                    background: "#ED6E00",
                    //keepShow: true,
                }
            },
            opsUsers: {
                vuescroll: {},
                scrollPanel: {},
                rail: {},
                bar: {
                    onlyShowBarOnScroll: true,
                    background: "#ED6E00",
                    //keepShow: true,
                }
            }
        };
    },
    validations: {
        newQuestion: {
            title: {
                required,
                maxLength: maxLength(5000),
            },
        },
    },
    mounted() {
        this.init ();

        let channel = Echo.channel('vote')
        channel.listen(".RegisteredUseEvent", (data) => {
            this.getRegisteredUsers ();
        });

        channel.listen(".NewVoteEvent", (data) => {
            this.handleNewVote (data.vote)
        });

        channel.listen(".VoteEndedEvent", (data) => {
            this.getActiveVote();
            this.getQuestions(this.activeEvent.id);
        });
    },
    methods: {
        ...mapMutations({
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        init () {
            this.getEvents();
            this.getRegisteredUsers();
            this.getActiveVote();
        },
        getRegisteredUsers () {
            this.setIsShowSpinner(true);
            axios
                .get(`/registered/event-users`)
                .then(response => {
                    this.registeredUsers = response.data.data.users;
                    this.registeredUsersGroups = response.data.data.groups;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        getEvents() {
            this.setIsShowSpinner(true);
            axios
                .get(`/events`)
                .then(response => {
                    this.events = response.data.data;

                    if (this.events.length) {
                        this.selectedEvent = this.events[0];
                        this.activeEvent = _find(this.events, ['started', 1]);

                        if (this.activeEvent) {
                            console.log(this.activeEvent)
                            this.getQuestions(this.activeEvent.id);
                            this.embedHtml = this.activeEvent.embedHtml;
                            this.voteTime = this.activeEvent.vote_time;
                        }
                    }
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        doActivateEvent () {

            if (!this.selectedEvent) {
                alert("Виберіть подію");
                return;
            }

            if (this.activeEvent) {
                alert("У вас вже є активна подія");
                return;
            }

            if (window.confirm("Активувати подію?")) {

                this.setIsShowSpinner(true);
                axios
                    .put(`events/${this.selectedEvent.id}`, {
                        title: this.selectedEvent.title,
                        started: 1,
                        finished: 0,
                    })
                    .then(response => {
                        this.activeEvent =_clone(response.data.data, true);

                        this.getRegisteredUsers ();

                        if (this.activeEvent) {
                            this.getQuestions(this.activeEvent.id);
                        }

                        this.getEvents();
                    })
                    .catch(error => {

                    })
                    .then(() => {
                        this.setIsShowSpinner(false);
                    });
            }
        },
        doDeactivateEvent () {
            if (!this.activeEvent) {
                return;
            }

            if (window.confirm("Деактивувати подію?")) {

                this.setIsShowSpinner(true);
                axios
                    .put(`events/${this.activeEvent.id}`, {
                        title: this.activeEvent.title,
                        started: 0,
                        finished: 1,
                    })
                    .then(response => {
                        this.activeEvent = null;
                        this.questions = [];
                        this.getEvents();
                    })
                    .catch(error => {

                    })
                    .then(() => {
                        this.setIsShowSpinner(false);
                    });
            }
        },
        getQuestions(eventId) {
            this.setIsShowSpinner(true);
            axios
                .get(`questions/events/${eventId}`)
                .then(response => {
                    console.log(response.data);
                    this.questions = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        saveEmbedHtml () {
            if (!this.selectedEvent) {
                alert ("Виберіть подію!");
                return;
            }

            this.setIsShowSpinner(true);
            axios
                .put(`events/${this.selectedEvent.id}`, {
                    title: this.selectedEvent.title,
                    embedHtml: this.embedHtmlModel,
                })
                .then(response => {
                    this.getEvents();
                    this.embedHtmlModel = '';
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },

        storeQuestion() {
            if (this.$v.newQuestion.title.$invalid) {
                this.$v.newQuestion.title.$touch();
                return;
            }
            this.setIsShowSpinner(true);
            axios
                .post(`questions`, {
                    title: this.newQuestion.title,
                    event_id: this.activeEvent.id,
                })
                .then(response => {
                    this.getQuestions(this.activeEvent.id);
                    this.isAddNewQuestion= false;
                })
                .catch(error => {

                })
                .then(() => {
                    this.newQuestion.title = "";
                    this.setIsShowSpinner(false);
                });
        },
        newVote(questionId) {
            // this.setIsShowSpinner(true);
            axios
                .post(`vote/new-vote`, {
                    question_id: questionId,
                })
                .then(response => {
                })
                .catch(error => {
                    // console.log(error.response.data.message);
                    alert(error.response.data.message);
                })
                .then(() => {
                    // this.setIsShowSpinner(false);
                });
        },
        saveVoteTime () {
            this.setIsShowSpinner(true);
            axios
                .put(`/events/${this.activeEvent.id}`, {
                    embedHtml: this.activeEvent.embedHtml,
                    finished: this.activeEvent.finished,
                    started: this.activeEvent.started,
                    title: this.activeEvent.title,
                    vote_time: this.voteTime,
                })
                .then(response => {
                    this.getEvents();
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        startTimer () {
            this.timerCurrentTime = this.activeVote.vote_time;
            this.timerWidth = 100;
            this.timerId = setInterval( () => {
                if (this.timerCurrentTime > 0) {
                    this.timerCurrentTime -= 0.1;
                    this.timerWidth = this.timerCurrentTime * 100 / this.activeVote.vote_time;
                } else {
                    clearInterval (this.timerId);
                }
            }, 100);
        },
        getActiveVote() {
            this.setIsShowSpinner(true);
            axios
                .get(`/vote/get-active-vote`)
                .then(response => {
                    this.activeVote = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },

        handleNewVote (vote) {
            this.activeVote = vote;
            console.log(this.activeVote);
            this.startTimer ();
        }
    }
}
</script>

<style scoped lang="scss">

.content {
    background-color: rgb(241, 241, 241);

    .main-section {
        height: calc(100vh - 200px);
    }
}

.registered-users-modal {
    z-index: 1000;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;

    .header {
        background-color: #256589;
    }
}

</style>

<style lang="scss">
    #iframe-wrapper {
        iframe {
            width: 100%;
            max-height: 300px;
        }
    }
</style>
