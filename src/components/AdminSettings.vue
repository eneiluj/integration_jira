<template>
	<div id="jira_prefs" class="section">
		<h2>
			<a class="icon icon-jira" />
			{{ t('integration_jira', 'Jira integration') }}
		</h2>
		<p class="settings-hint">
			{{ t('integration_jira', 'If you want to allow your Nextcloud users to use OAuth to authenticate to Jira, create an application in your Jira admin settings and set the ID and secret here.') }}
			<a class="external" href="https://developer.atlassian.com">
				{{ t('integration_jira', 'Jira app settings') }}
			</a>
			<br><br>
			<span class="icon icon-details" />
			{{ t('integration_jira', 'Make sure you set the redirection/callback URL to') }}
			<b> {{ redirect_uri }} </b>
			<br><br>
			<span class="icon icon-details" />
			{{ t('integration_jira', 'Don\'t forget to make your Jira OAuth application public.') }}
			<a class="external" href="https://developer.atlassian.com/cloud/jira/platform/oauth-2-authorization-code-grants-3lo-for-apps/#publishing-your-oauth-2-0--3lo--app">
				{{ t('integration_jira', 'How to make Jira OAuth public') }}
			</a>
			<br><br>
			{{ t('integration_jira', 'Put the "Client ID" and "Client secret" below. Your Nextcloud users will then see a "Connect to Jira" button in their personal settings.') }}
		</p>
		<div class="grid-form">
			<label for="jira-client-id">
				<a class="icon icon-category-auth" />
				{{ t('integration_jira', 'Client ID') }}
			</label>
			<input id="jira-client-id"
				v-model="state.client_id"
				type="password"
				:readonly="readonly"
				:placeholder="t('integration_jira', 'ID of your application')"
				@focus="readonly = false"
				@input="onInput">
			<label for="jira-client-secret">
				<a class="icon icon-category-auth" />
				{{ t('integration_jira', 'Client secret') }}
			</label>
			<input id="jira-client-secret"
				v-model="state.client_secret"
				type="password"
				:readonly="readonly"
				:placeholder="t('integration_jira', 'Your application secret')"
				@focus="readonly = false"
				@input="onInput">
		</div>
		<br>
		<div class="grid-form">
			<label for="jira-forced-instance">
				<a class="icon icon-link" />
				{{ t('integration_jira', 'Restrict self hosted URL to') }}
			</label>
			<input id="jira-forced-instance"
				v-model="state.forced_instance_url"
				type="text"
				:placeholder="t('integration_jira', 'Instance address')"
				@input="onInput">
		</div>
	</div>
</template>

<script>
import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { delay } from '../utils'
import { showSuccess, showError } from '@nextcloud/dialogs'
import '@nextcloud/dialogs/styles/toast.scss'

export default {
	name: 'AdminSettings',

	components: {
	},

	props: [],

	data() {
		return {
			state: loadState('integration_jira', 'admin-config'),
			// to prevent some browsers to fill fields with remembered passwords
			readonly: true,
			redirect_uri: window.location.protocol + '//' + window.location.host + generateUrl('/apps/integration_jira/oauth-redirect'),
		}
	},

	watch: {
	},

	mounted() {
	},

	methods: {
		onInput() {
			delay(() => {
				this.saveOptions()
			}, 2000)()
		},
		saveOptions() {
			const req = {
				values: {
					client_id: this.state.client_id,
					client_secret: this.state.client_secret,
					forced_instance_url: this.state.forced_instance_url,
				},
			}
			const url = generateUrl('/apps/integration_jira/admin-config')
			axios.put(url, req)
				.then((response) => {
					showSuccess(t('integration_jira', 'Jira admin options saved'))
				})
				.catch((error) => {
					showError(
						t('integration_jira', 'Failed to save Jira admin options')
						+ ': ' + error.response.request.responseText
					)
				})
				.then(() => {
				})
		},
	},
}
</script>

<style scoped lang="scss">
.grid-form label {
	line-height: 38px;
}

.grid-form input {
	width: 100%;
}

.grid-form {
	max-width: 500px;
	display: grid;
	grid-template: 1fr / 1fr 1fr;
	margin-left: 30px;
}

#jira_prefs .icon {
	display: inline-block;
	width: 32px;
}

#jira_prefs .grid-form .icon {
	margin-bottom: -3px;
}

.icon-jira {
	background-image: url(./../../img/app-dark.svg);
	background-size: 23px 23px;
	height: 23px;
	margin-bottom: -4px;
}

body.theme--dark .icon-jira {
	background-image: url(./../../img/app.svg);
}

</style>
