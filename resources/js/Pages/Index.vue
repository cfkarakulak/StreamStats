<template>
  <div class="container">
    <div class="greeting">
      <h2>Welcome to StreamStats!</h2>
    </div>

    <div class="attributes">
      <div class="authorization">
        <a :href="auth_uri">
          <svg width="256px" height="268px" viewBox="0 0 256 268" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid">
            <g><path fill="#FFF" d="M17.4579119,0 L0,46.5559188 L0,232.757287 L63.9826001,232.757287 L63.9826001,267.690956 L98.9144853,267.690956 L133.811571,232.757287 L186.171922,232.757287 L256,162.954193 L256,0 L17.4579119,0 Z M40.7166868,23.2632364 L232.73141,23.2632364 L232.73141,151.29179 L191.992415,192.033461 L128,192.033461 L93.11273,226.918947 L93.11273,192.033461 L40.7166868,192.033461 L40.7166868,23.2632364 Z M104.724985,139.668381 L127.999822,139.668381 L127.999822,69.843872 L104.724985,69.843872 L104.724985,139.668381 Z M168.721862,139.668381 L191.992237,139.668381 L191.992237,69.843872 L168.721862,69.843872 L168.721862,139.668381 Z"></path></g>
          </svg>
          <span>Login with Twitch</span>
        </a>
      </div>

      <div class="profile" v-if="authenticated">
        <ul>
          <li>
            <small>Your username</small>
            <span>{{ user.login }}</span>
          </li>

          <li>
            <small>Your email</small>
            <span>{{ user.email }}</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="stats" v-if="authenticated">
      <div class="grouped">
        <div class="streams-by-game">
          <div class="question">
            <small>Total number of streams for each game</small>
          </div>

          <div class="answer">
            <streams-by-games :data="data.streams_by_games"></streams-by-games>
          </div>
        </div>

        <div class="games-by-viewers">
          <div class="question">
            <small>Top games by viewer count for each game</small>
          </div>

          <div class="answer">
            <games-by-viewers :data="data.games_by_viewers"></games-by-viewers>
          </div>
        </div>

        <div class="streams-by-nearest-hours">
          <div class="question">
            <small>Total number of streams by their start time (rounded to the nearest hour)</small>
          </div>

          <div class="answer">
            <streams-by-nearest-hours :data="data.streams_by_nearest_hours"></streams-by-nearest-hours>
          </div>
        </div>
      </div>

      <div class="individual">
        <div class="streams-by-viewers">
          <div class="question">
            <small>List of top 100 streams by viewer count that can be sorted asc & desc</small>
          </div>

          <div class="answer">
            <streams-by-viewers :data="data.streams_by_viewers"></streams-by-viewers>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import StreamsByGames from "../Components/StreamsByGames.vue";
import StreamsByViewers from "../Components/StreamsByViewers.vue";
import GamesByViewers from "../Components/GamesByViewers.vue";
import StreamsByNearestHours from "../Components/StreamsByNearestHours.vue";

export default {
  components: {
    StreamsByGames,
    StreamsByViewers,
    GamesByViewers,
    StreamsByNearestHours,
  },
  props: {
    auth_uri: {
      type: String,
      required: true,
    },
    authenticated: {
      type: Boolean,
      required: true,
    },
    user: {
      type: Object,
    },
    data: {
      type: Object,
    },
  },
}
</script>