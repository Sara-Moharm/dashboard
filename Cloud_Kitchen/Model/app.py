from transformers import AutoTokenizer, AutoModelForSequenceClassification
import torch
from flask import Flask, request, jsonify
from flask_cors import CORS  # Enable CORS for Laravel requests
import os
import numpy as np
import re
import traceback

SENTIMENT_LABELS = {
    0: "negative",
    1: "neutral",
    2: "positive"
}

def get_aspect_keywords():
    
    """Return a dictionary of aspects with their related keywords"""
    return {
        "Food Quality": [
            "food", "meal", "dish", "taste", "flavor", "fresh", "cold", "hot", "raw", "burnt",
            "salty", "sweet", "spicy", "bland", "delicious", "tasty", "bad", "good", "portion", "quality", "ingredients"
        ],

        "Delivery Experience": [
            "delivery", "delivered", "driver", "rider", "courier", "late", "on time", "fast", "delay",
            "tracking", "estimated", "wrong address", "location", "handed", "left outside", "didn't arrive"
        ],

        "Packaging": [
            "packaging", "package", "sealed", "leaked", "spilled", "box", "bag", "messy", "clean",
            "secure", "presentation", "damaged", "intact", "wet", "broken"
        ],

        "Price/Value": [
            "price", "cost", "expensive", "cheap", "affordable", "worth", "value", "overpriced", "reasonable", 
            "discount", "offer", "deal", "promo", "charges", "fees"
        ],

        "Order Accuracy": [
            "order", "wrong", "missing", "item", "correct", "extra", "forgot", "included", "mistake",
            "issue", "replacement", "wrong item", "not what I ordered"
        ],

        "Customer Support": [
            "support", "help", "chat", "call", "complain", "response", "rude", "friendly", "agent",
            "resolved", "ignored", "ticket", "unhelpful", "follow up"
        ],

        "App/Platform Experience": [
            "app", "website", "easy", "hard", "navigate", "bug", "crash", "payment", "checkout",
            "interface", "search", "filter", "glitch", "slow", "user-friendly"
        ]
    }


def extract_sentences_for_keywords(text, keywords):
    """Extract sentences that mention any of the keywords"""
    sentences = re.split(r"(?<=[.!?])\s+", text)
    relevant = []
    for sentence in sentences:
        if any(re.search(rf"\b{re.escape(keyword)}\b", sentence.lower()) for keyword in keywords):
            relevant.append(sentence)
    return relevant


def extract_aspects(text):
    """Return a dict of aspects with their related sentences"""
    aspects = get_aspect_keywords()
    results = {}

    for aspect, keywords in aspects.items():
        sentences = extract_sentences_for_keywords(text, keywords)
        if sentences:
            results[aspect] = sentences     

    if not results:
        results["uncategorized"] = [text]
    return results



app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})  # Allow all requests from any origin

# Load the trained model and vectorizer
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_DIR = os.path.join(BASE_DIR, "sentiment_model")

try:
    tokenizer = AutoTokenizer.from_pretrained(MODEL_DIR)
    model = AutoModelForSequenceClassification.from_pretrained(MODEL_DIR)
    model.eval()

except Exception as e:
    print(f"Error loading model or tokenizer: {e}")
    with open("model_error.log", "w") as f:
        traceback.print_exc(file=f)
    tokenizer, model = None, None

@app.route("/", methods=["GET"])
def home():
    return jsonify({"message": "Flask API is running!"})

@app.route("/predict", methods=["POST"])
def predict():
    try:
        if not model or not tokenizer:
            return jsonify({"error": "Model or tokenizer not loaded."}), 500

        data = request.get_json()
        if not data or "text" not in data:
            return jsonify({"error": "Missing 'text' field."}), 400

        # Analyze The Whole Text
        text = data["text"]
        inputs = tokenizer(text, return_tensors="pt", truncation=True, padding=True)

        with torch.no_grad():
         outputs = model(**inputs)
         prediction_idx = torch.argmax(outputs.logits, dim=1).item()
         prediction = SENTIMENT_LABELS.get(prediction_idx, "unprocessed")

        # Analyze Aspects
        extracted_aspects = extract_aspects(text)
    
       
        if extracted_aspects:   
         result = {}
         for aspect, sentences in extracted_aspects.items():
            result[aspect] = []
            for sentence in sentences:
                input = tokenizer(sentence, return_tensors="pt", truncation=True, padding=True)

                with torch.no_grad():
                 output = model(**input)
                 sentiment_idx = torch.argmax(output.logits, dim=1).item()
                 sentence_sentiment = SENTIMENT_LABELS.get(sentiment_idx, "unprocessed")
                 
                result[aspect].append({
                    "sentence": sentence,
                    "sentiment": sentence_sentiment
            })

        # Convert NumPy int64 to Python int
        return jsonify({"prediction": prediction
                        , "aspects": result}), 200

    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)


 