import React from 'react';
import PropTypes from 'prop-types';
import deburr from 'lodash/deburr';
import Downshift from 'downshift';
import {makeStyles} from '@material-ui/core/styles';
import TextField from '@material-ui/core/TextField';
import Popper from '@material-ui/core/Popper';
import Paper from '@material-ui/core/Paper';
import MenuItem from '@material-ui/core/MenuItem';
import * as SmartyStreetsSDK from "smartystreets-javascript-sdk";

const smartyStreetsSharedCredentials = new SmartyStreetsSDK.core.SharedCredentials(4750402551188757);
const autoCompleteClientBuilder = new SmartyStreetsSDK.core.ClientBuilder(smartyStreetsSharedCredentials);
const autoCompleteClient = autoCompleteClientBuilder.buildUsAutocompleteClient();




export default function IntegrationDownshift(props) {

    const useStyles = makeStyles(theme => ({
        root: {
            flexGrow: 1,
        },
        container: {
            flexGrow: 1,
            position: 'relative',
        },
        paper: {
            position: 'absolute',
            zIndex: 1,
            marginTop: theme.spacing(1),
            left: 0,
            right: 0,
            width: props.width ?? props.width,
        },
        chip: {
            margin: theme.spacing(0.5, 0.25),
        },
        inputRoot: {
            flexWrap: 'wrap',
        },
        inputInput: {
            width: 'auto',
            flexGrow: 1,
        },
        divider: {
            height: theme.spacing(2),
        },
    }));
    const [suggestions, setSuggestions] = React.useState([]);
    const [value, setValue] = React.useState('');
    const [getFinal, setGetFinal] = React.useState([]);


    React.useEffect(() => {
        sendDatatoParent();
    },[getFinal,value])


    const sendDatatoParent = () => {
        // const [addressobject, addressvalue] = props;
        if(getFinal !== undefined && getFinal.length !== 0) {
            props.addressObject(getFinal)
        } else {
            props.addressObject(value)
        }
    }



    const handleStateChange = changes => {
        if (changes.hasOwnProperty('selectedItem')) {
            setValue(changes.selectedItem);
            const value = changes.selectedItem;
            const newObject = suggestions.find((value) => value.text === changes.selectedItem);
            setGetFinal(newObject);
        } else if (changes.hasOwnProperty('inputValue')) {
            setGetFinal([]);
            setValue(changes.inputValue);
        }

    }


    const smartyHandler = async (event) => {
        const lookup = await new SmartyStreetsSDK.usAutocomplete.Lookup(event.target.value);
        autoCompleteClient.send(lookup)
            .then(response => {
                const add = response.result
                setSuggestions(add);
            })
    }


    function renderInput(inputProps) {
        const {disabled, required, error, className} = props
        const {InputProps, classes, ref, ...other} = inputProps;
        return (
            <TextField
                className={className}
                disabled={disabled}
                required
                onChange={(event) => smartyHandler(event)}
                InputProps={{
                    inputRef: ref,
                    classes: {
                        root: classes.inputRoot,
                        input: classes.inputInput,
                    },
                    ...InputProps,
                }}
                {...other}
            />
        );
    }

    function renderSuggestion(suggestionProps) {

        const {suggestion, index, itemProps, highlightedIndex, selectedItem} = suggestionProps;
        const isHighlighted = highlightedIndex === index;
        const isSelected = (selectedItem || '').indexOf(suggestion.label) > -1;
        return (
            <MenuItem

                {...itemProps}
                selected={isHighlighted}
                key={suggestion.text}
                component="div"
                style={{fontWeight: isSelected ? 500 : 400,}}>
                {suggestion.text}
            </MenuItem>
        );
    }

    function getSuggestions(value, {showEmpty = false} = {}) {
        const inputValue = deburr(value.trim()).toLowerCase();
        const inputLength = inputValue.length;

        let count = 0;

        return inputLength === 0 && !showEmpty
            ? inputValue
            : suggestions.filter(suggestion => {
                const keep =
                    count < 5 && suggestion.text.slice(0, inputLength).toLowerCase() === inputValue;
                if (keep) {
                    count += 1;
                }
                return keep;
            });

    }

    const classes = useStyles();
    return (
        <div   className={classes.root}>
            <Downshift selectedItem={value} onStateChange={handleStateChange} id="downshift-simple">
                {({
                      getInputProps,
                      getItemProps,
                      getLabelProps,
                      getMenuProps,
                      highlightedIndex,
                      inputValue,
                      isOpen,
                      selectedItem,
                      clearSelection,
                      getRootProps,
                      onInputValueChange,
                  }) => {
                    const {onBlur, onFocus, ...inputProps} = getInputProps({
                        placeholder: 'Address',
                    });
                    return (
                        <div className={classes.container}>
                            {renderInput({
                                fullWidth: true,
                                classes,
                                label: 'Address',
                                InputLabelProps: getLabelProps({shrink: true}),
                                InputProps: {onBlur, onFocus},
                                inputProps,
                            })}

                            <div {...getMenuProps()}>
                                {isOpen ? (
                                    <Paper className={classes.paper} square>
                                        {inputValue !== '' ? getSuggestions(inputValue)?.map((suggestion, index) =>
                                            renderSuggestion({
                                                suggestion,
                                                index,
                                                itemProps: getItemProps({item: suggestion.text, index}),
                                                highlightedIndex,
                                                selectedItem,

                                            }),
                                        ) : null}
                                    </Paper>
                                ) : null}
                            </div>
                        </div>
                    );
                }}
            </Downshift>
        </div>
    );
}
