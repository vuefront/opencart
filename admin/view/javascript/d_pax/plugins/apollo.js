import Vue from 'vue'
import VueApollo from 'vue-apollo'
import ApolloClient from "apollo-boost";
export default ({ tokenUrl, store }, inject) => {

  const apolloClient = new ApolloClient({
    uri: `index.php?route=module/d_vuefront/proxy&${tokenUrl}`,
    request: (operation) => {
      const headers = {}
      if (
        store.getters['auth/isLogged']
      ) {
        headers['token'] = `${
          store.getters['auth/isLogged']
        }`
      }
      operation.setContext({
        headers
      });
    }
  })

  Vue.use(VueApollo)
  const apolloProvider = new VueApollo({
    defaultClient: apolloClient
  })

  inject('apolloClient', apolloProvider)
}
