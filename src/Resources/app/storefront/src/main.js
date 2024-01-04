import InpostLockerSelector from './plugins/inpost-locker-selector';

const PluginManager = window.PluginManager;
PluginManager.register('InpostLockerSelector', InpostLockerSelector, '#easypack-widget');
