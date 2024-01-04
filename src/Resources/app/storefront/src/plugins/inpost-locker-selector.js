import Plugin from 'src/plugin-system/plugin.class'
import HttpClient from 'src/service/http-client.service'
import StoreApiClient from 'src/service/store-api-client.service'
import $ from 'jquery'

export default class InpostLockerSelector extends Plugin {
    init () {
        const me = this

        me.$el = $(me.el)
        me._client = new HttpClient()
        me._storeApiClient = new StoreApiClient()
        me.registerEvents()

        me.$modal = $(me.$el.data('inpost-locker-modal-selector'))
        me.$address = $(me.$el.data('inpost-locker-address-selector'))
    }

    registerEvents () {
        const me = this

        window.onload = function () {
            window.easyPack.dropdownWidget('easypack-widget', function (point) {
                me.selectLocker(point)
            })
        }
    }

    selectLocker (point) {
        const me = this;
        const data = {
            locker: point
        }

        // me.toggleOverlay()

        me._storeApiClient.post('/sales-channel-api/v3/inpost/select-locker', JSON.stringify(data), response => {
            const parsed = JSON.parse(response)

            me.$address.html(parsed['view'])
            me.$modal.modal('hide')

            $('#confirmFormSubmit').prop('disabled', false)

            // me.toggleOverlay()
        })
    }

    // toggleOverlay () {
    //   const me = this
    //
    //   const $overlay = me.$modal.find('.modal-overlay')
    //
    //   if ($overlay) {
    //     $overlay.toggleClass('is-loading')
    //   }
    // }
}
