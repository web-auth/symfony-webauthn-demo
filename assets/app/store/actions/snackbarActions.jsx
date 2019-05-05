import { ENQUEUE_SNACKBAR, REMOVE_SNACKBAR } from './actionTypes'

export const enqueueSnackbar = notification => ( {
    notification: {
        key: new Date().getTime() + Math.random(),
        ...notification,
    },
    type: ENQUEUE_SNACKBAR,
} )

export const removeSnackbar = key => ( {
    key,
    type: REMOVE_SNACKBAR,
} )
