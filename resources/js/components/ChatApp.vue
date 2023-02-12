<template>
  <div class="container-chat clearfix">
    <div class="people-list" id="people-list">
      <div class="search">
        <input type="text" placeholder="search" />
      </div>
      <ul class="list">
        <li
          @click="selectUser(user.id)"
          class="clearfix"
          v-for="user in userList"
          :key="user.id"
        >
          <img
            style="
              width: 50px;
              height: 50px;
              border-radius: 50%;
              object-fit: cover;
            "
            :src="
              user.image
                ? user.image
                : 'https://upload.wikimedia.org/wikipedia/commons/8/89/Portrait_Placeholder.png'
            "
            alt="avatar"
          />
          <div class="about">
            <div class="name">{{ user.name }}</div>
            <div class="status">
              <span v-if="onlineUser(user.id) || online.id == user.id"
                ><i class="fa fa-circle onlinecolor"></i> online</span
              >

              <span v-else
                ><i else class="fa fa-circle offlinecolor"></i> offline</span
              >
            </div>
          </div>
        </li>
      </ul>
    </div>

    <div v-if="userMessage.user" class="chat">
      <div class="chat-header clearfix">
        <img
          v-if="userMessage.user"
          style="width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
          "
          :src="
            userMessage.user.image
              ? userMessage.user.image
              : 'https://upload.wikimedia.org/wikipedia/commons/8/89/Portrait_Placeholder.png'
          "
          alt="avatar"
        />

        <div class="chat-about">
          <div v-if="userMessage.user" class="chat-with">
            {{ userMessage.user.name }}
          </div>
          <div class="chat-num-messages">
            <span  v-if="userMessage.messages">
                <span v-if="userMessage.messages.length > 0">
                Total {{ userMessage.messages.length }} Messages
            </span>
            <span v-else>No Messages</span>
            </span>

        </div>
        </div>
        <div v-if="userMessage.user" class="actionDropdown">
            <div  class="dropdown dropleft deleteAllMessage">
          <a
            href="#"
            id="dropdownMenuLink"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
          <i class="fa-solid fa-circle-chevron-down"></i>
          </a>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <a
              class="dropdown-item"
              href="#"
              @click.prevent="deleteAllMessage(userMessage.user.id)"
              >Delete All Message</a
            >
          </div>
        </div>
        </div>

      </div>
      <!-- end chat-header -->

      <div class="chat-history" v-chat-scroll>
        <ul>
          <li
            v-for="message in userMessage.messages"
            :key="message.id"
            class="clearfix"
          >
            <div
              class="message-data"
              :class="`${
                message.user.id == userMessage.user.id
                  ? 'float-left align-left'
                  : 'float-right align-right'
              }`"
            >
              <div>
                <span v-if="message.user.id == userMessage.user.id" class="message-data-name">{{
                  message.user.name
                }}</span>,
                <span class="message-data-time">{{
                  message.updated_at | timeformat
                }}</span>
                <span style="display : inline-block" class="actionDropdown">
                  <div class="dropdown">
                    <a
                      href="#"
                      id="dropdownMenuLink"
                      data-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false"
                    >
                    <i class="fa-solid fa-circle-chevron-down"></i>
                    </a>
                    <div
                      class="dropdown-menu"
                      aria-labelledby="dropdownMenuLink"
                    >
                      <a
                        class=""
                        href="#"
                        @click.prevent="deleteSingleMessage(message.id)"
                        >Delete</a
                      >
                    </div>
                  </div>
                </span>
              </div>
            </div>
            <div
              :class="`message  ${
                message.user.id == userMessage.user.id
                  ? 'my-message float-left'
                  : 'other-message float-right'
              }`"
            >
              {{ message.message }}
            </div>
          </li>
          <div v-if="typing" class="chat-bubble">
            <div class="typing">
              <div class="dot"></div>
              <div class="dot"></div>
              <div class="dot"></div>
            </div>
          </div>
        </ul>
      </div>
      <!-- end chat-history -->
      <input type="file" style="display : none" id="uploadFile">
      <div class="chat-message clearfix">
        <label class="file-up-icon" for="uploadFile">
            <!-- <i class="fa-solid fa-image"></i> -->
        </label>
        <textarea
          @keydown.enter.prevent="sendMessage"
          @keydown="typingEvent(userMessage.user.id)"
          v-model="message"
          name="message-to-send"
          id="message-to-send"
          placeholder="Type your message"
          rows="3"
        ></textarea>
        <span class="sendmessageIcon"><i @click.prevent="sendMessage" class="fa-solid fa-paper-plane"></i></span>
      </div>
      <!-- end chat-message -->
    </div>
    <div v-else class="chaBlank">
        <p>Please select a user <br> to start chat.</p>
    </div>
    <!-- end chat -->
  </div>
  <!-- end container -->
</template>

<script>
import Axios from "axios";
import _ from 'lodash';
export default {
  data() {
    return {
      message: "",
      typing: "",
      users: [],
      online: "",
    };
  },
  mounted() {
    Echo.private(`chat.${authuser.id}`).listen("MessageSend", (e) => {
      console.log(e);
      this.selectUser(e.message.from);
    });

    Echo.private("typingevent").listenForWhisper("typing", (e) => {
      if (e.user.id == this.userMessage.user.id && e.userId == authuser.id) {
        this.typing = e.user.name;
      }
      setTimeout(() => {
        this.typing = "";
      }, 2000);
    });

    Echo.join("liveuser")
      .here((users) => {
        this.users = users;
      })
      .joining((user) => {
        this.online = user;
      })
      .leaving((user) => {
        // this.online = '';
      });

    this.$store.dispatch("userList");
  },
  computed: {
    userList() {
      return this.$store.getters.userList;
    },
    userMessage() {
      return this.$store.getters.userMessage;
    },
  },
  methods: {
    selectUser(id) {
      this.$store.dispatch("userMessage", id);
    },
    sendMessage() {
      console.log(this.message);
      if (this.message != "") {
        Axios.post("/sendmessage", {
          message: this.message,
          user_id: this.userMessage.user.id,
        }).then((res) => {
          this.selectUser(this.userMessage.user.id);
          console.log(res.data);
          this.message = "";
        });
      }
    },
    deleteSingleMessage(id) {
      Axios.get("/deletesinglemessage/" + id).then((res) => {
        this.selectUser(this.userMessage.user.id);
      });
    },
    deleteAllMessage(id) {
      Axios.get("/deleteallemessage/" + id).then((res) => {
        this.selectUser(this.userMessage.user.id);
      });
    },
    typingEvent(userId) {
      Echo.private("typingevent").whisper("typing", {
        user: authuser,
        typing: this.message,
        userId: userId,
      });
    },
    onlineUser(id){
        return  _.find(this.users, {'id' : id});
    }
  },
};
</script>

<style>
</style>
