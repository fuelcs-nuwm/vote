<template>
    <div class="chat d-flex flex-column flex-grow-1 border border-secondary">
        <div class="bg-primary p-2 text-center" role="button" @click="getMessages">
            Оновити чат
        </div>
        <div class="chat-body d-flex p-3">
            <div class="flex-grow-1 border-bottom  border-top border-secondary">
                <vuescroll ref="vuescroll" :ops="ops">
                    <div v-for="message in messages">
                        <div class="d-flex flex-column border border-secondary my-2">
                            <div class="bg-white p-2">
                                <span><b>{{ message.user.name }}</b></span><br><span>{{ message.date }}</span>
                            </div>
                            <div class="flex-grow-1 p-2">{{ message.message }}</div>
                            <div class="d-flex justify-content-end">
                                <span
                                    class="p-2 border-top border-left"
                                    role="button"
                                    @click="replyMessage(message.id)"
                                >Відповісти</span>
                            </div>
                        </div>

                        <div
                            v-for="replies_message in message.replies"
                            class="d-flex flex-column border border-secondary my-2 ml-5"
                        >
                            <div class="bg-white p-2">
                                <span><b>{{ replies_message.user.name }}</b></span><br><span>{{ replies_message.date }}</span>
                            </div>
                            <div class="flex-grow-1 p-2">{{ replies_message.message }}</div>
                        </div>
                    </div>

                </vuescroll>
            </div>
        </div>
        <div class="input-group p-3">
                <textarea
                    type="text"
                    class="form-control"
                    :placeholder="isReply ? 'Нова відповідь':'Нове запитання'"
                    v-model="newMessage.message"
                    rows="3"
                ></textarea>
            <div class="input-group-append">
                <span v-if="!isReply" class="input-group-text" role="button" @click="storeMessage">Надіслати</span>
                <div v-if="isReply" class="input-group-text d-flex flex-column" role="button">
                    <span @click="storeMessage">Відповісти</span>
                    <span
                        class="p-2"
                        @click="cancelReply"
                    >Скасувати</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import vuescroll from "vuescroll";
import {mapMutations} from "vuex";
import {maxLength, required} from "vuelidate/lib/validators";
export default {
    components: {
        vuescroll,
    },
    data() {
        return {
            messages: [],
            newMessage: {
                message: ""
            },

            isReply: false,
            replyMessageId: null,

            ops: {
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
        newMessage: {
            message: {
                required,
                maxLength: maxLength(5000),
            },
        },
    },
    mounted() {
        this.init();

        let channel = Echo.channel('chat')
        channel.listen(".NewMessageEvent", (data) => {
            this.getMessages();
        });
    },
    methods: {
        ...mapMutations({
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        init() {
            this.getMessages();
        },
        getMessages() {
            this.setIsShowSpinner(true);
            axios
                .get(`/messages/event-messages`)
                .then(response => {
                    this.messages = response.data.data;
                    this.$nextTick(()=>{
                        this.$refs["vuescroll"].scrollTo(
                            {
                                y: "100%"
                            },
                            500
                        );
                    })

                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        storeMessage() {
            if (!this.newMessage.message) {
                return;
            }
            this.setIsShowSpinner(true);
            axios
                .post(`messages`, {
                    message: this.newMessage.message,
                    message_id: this.replyMessageId,
                })
                .then(response => {
                })
                .catch(error => {

                })
                .then(() => {
                    this.newMessage.message = "";
                    this.isReply = false;
                    this.replyMessageId = null;
                    this.setIsShowSpinner(false);
                });
        },
        replyMessage (message_id) {
            this.isReply = true;
            this.replyMessageId = message_id;
        },
        cancelReply () {
            this.isReply = false;
            this.replyMessageId = null;
        }
    }
}
</script>

<style scoped lang="scss">
    .chat-body {
        max-height: calc(100vh - 400px);

        @media (max-width: 767.98px) {
            max-height: calc(100vh - 200px);
        }

        @media (max-width: 575.98px) {
            max-height: calc(100vh - 300px);
        }

    }
</style>
